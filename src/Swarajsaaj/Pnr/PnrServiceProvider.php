<?php namespace Swarajsaaj\Pnr;

use Illuminate\Support\ServiceProvider;

class PnrServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('swaraj/pnr');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		  $this->app['pnr'] = $this->app->share(function($app)
        {
            return new Pnr;
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('pnr');
	}

}
