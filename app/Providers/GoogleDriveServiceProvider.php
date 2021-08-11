<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Hypweb\Flysystem\GoogleDrive\GoogleDriveAdapter;
use League\Flysystem\Filesystem;

class GoogleDriveServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap the application services.
   *
   * @return void
   */
  public function boot()
  {
    Storage::extend('google', function ($app, $config) {
      $client = new \Google_Client();
      $client->setClientId($config['clientId']);
      $client->setClientSecret($config['clientSecret']);
      $client->refreshToken($config['refreshToken']);
      $client->fetchAccessTokenWithRefreshToken($config['refreshToken']);
      $client->setAccessType('online');
      $client->setApprovalPrompt('force');
      $service = new \Google_Service_Drive($client);
      $adapter = new GoogleDriveAdapter($service, $config['folderId']);

      return new Filesystem($adapter);
    });
  }

  /**
   * Register the application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }
}
