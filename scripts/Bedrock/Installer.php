<?php

namespace Bedrock;

use Composer\Script\Event;

class Installer {
  public static $KEYS = array(
    'AUTH_KEY',
    'SECURE_AUTH_KEY',
    'LOGGED_IN_KEY',
    'NONCE_KEY',
    'AUTH_SALT',
    'SECURE_AUTH_SALT',
    'LOGGED_IN_SALT',
    'NONCE_SALT'
  );

  public static function addSalts(Event $event) {
    $root = dirname(dirname(__DIR__));
    $composer = $event->getComposer();
    $io = $event->getIO();

    $generate_salts = $composer->getConfig()->get('generate-salts');
    

    if (!$generate_salts) {
      return 1;
    }

    $salts = array_map(function ($key) {
      return sprintf("%s='%s'", $key, Installer::generate_salt());
    }, self::$KEYS);

    $env_file = "{$root}/.env";

    //Check if file exists and doesn't have salts yet
    if (file_exists($env_file) && strlen(strpos(file_get_contents($env_file),self::$KEYS[0])) == 0) {
      Installer::appendEnv(implode($salts, "\n"));
      $io->write(".env found without salts. Appended salts");
    } elseif (!file_exists($env_file) && file_exists("{$root}/.env.template")) {
      copy("{$root}/.env.template", $env_file);
      Installer::appendEnv(implode($salts, "\n"));
      $io->write("Appending salts to Template");
    } elseif (!file_exists($env_file)) {
      $io->write("No Template found. Writing a new .env file");
      Installer::appendEnv(implode($salts, "\n"));
    }
  }

  /**
   * Slightly modified/simpler version of wp_generate_password
   * https://github.com/WordPress/WordPress/blob/cd8cedc40d768e9e1d5a5f5a08f1bd677c804cb9/wp-includes/pluggable.php#L1575
   */
  public static function generate_salt($length = 64) {
    $chars  = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $chars .= '!@#$%^&*()';
    $chars .= '-_ []{}<>~`+=,.;:/?|';

    $salt = '';
    for ($i = 0; $i < $length; $i++) {
      $salt .= substr($chars, rand(0, strlen($chars) - 1), 1);
    }

    return $salt;
  }
  /**
   * When using wp package with languages they should be transferred to right location.
   */
  public static function installLanguages(Event $event) {
    $root = dirname(dirname(__DIR__));
    $from = "{$root}/web/wp/wp-content/languages";
    $to   = "{$root}/web/app/languages";
    $io = $event->getIO();
    $env_file ="{$root}/.env";
    if (file_exists($from) and is_dir($from)) {
      if(!file_exists($to)) {
        rename($from, $to);
        $io->write("Moved languages from wp install");
        $io->write("Searching languagefile with: {$to}/{??_??,???,??}.mo");
        foreach (glob("{$to}/{??_??,???,??}.mo", GLOB_BRACE) as $languagefile) {
          $language = substr($languagefile, strlen($to)+1, -3); //cut out directory and .mo
          Installer::appendEnv("WP_LANGUAGE={$language}");
          $io->write("And added WP_LANGUAGE={$language}");
        }
      } else {
        $io->write("languages dir exists! I won't overwrite it");

      }
    }
  }
  /**
  * Basic ENVVAR appending. Wanted to have some layers here so I can make it better in future.
  */
  public static function appendEnv($line){
    $root = dirname(dirname(__DIR__));
    $env_file ="{$root}/.env";
    file_put_contents($env_file, "\n{$line}\n", FILE_APPEND | LOCK_EX);
  }
  /**
  * Idea is to move everything out of -nested-**** folders until I find better way to manage them
  */
  public static function installPrivateThemes(Event $event){
    $rule = "\-nested\-";
    $io = $event->getIO();
    $root = dirname(dirname(__DIR__));
    $from = "{$root}/web/app/themes/";
    //Move all folders from theme folders and overwrite earlier themes
    foreach (glob("{$from}{$rule}*/[0-9a-zA-Z]*") as $theme) {
      if (is_dir($theme)) {
        $filename = substr( $theme, strrpos( $theme, '/' )+1 );
        if (file_exists("{$from}{$filename}")) {
          $io->write("overwriting theme from vcs: {$filename}");
          exec("rm -rf {$from}{$filename}");
        }
        rename($theme, "{$from}{$filename}");
      }
    }
    //Remove all private theme folders == folders matching $rule
    foreach (glob("{$from}{$rule}*/") as $directory) {
      if (is_dir($directory)) {
        $io->write("Deleting private theme-bundle vcs: {$directory}");
        exec("rm -rf {$directory}");
      }
    }
  }
}
