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
      $client->setClientId(env('GOOGLE_DRIVE_CLIENT_ID'));
      $client->setClientSecret(env('GOOGLE_DRIVE_CLIENT_SECRET'));
      $client->refreshToken(env('GOOGLE_DRIVE_REFRESH_TOKEN'));
      $client->setRedirectUri($this->_redirectURI);
      $client->setAccessType('offline');
      $client->setApprovalPrompt('force');
      $service = new \Google_Service_Drive($client);

      $options = [];
      if (isset($config['teamDriveId'])) {
        $options['teamDriveId'] = $config['teamDriveId'];
      }

      $adapter = new GoogleDriveAdapter($service, $config['folderId'], $options);

      return new \League\Flysystem\Filesystem($adapter);
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
