For basic setting option

php artisan make:migration create_settings_table


	Schema::create('settings', function (Blueprint $table) {
	    $table->id();
	    $table->string('site_name')->nullable();
	    $table->string('facebook_link')->nullable();
	    $table->string('twitter_link')->nullable();
	    $table->timestamps();
	});

php artisan migrate

php artisan make:model Setting

	protected $fillable = [
		'site_name',
		'facebook_link',
		'twitter_link',
	];

php artisan make:controller Admin/SettingsController

API call,

For admin panel,

	POST /api/admin/login
	GET /api/admin/dashboard
	POST /api/admin/change-password
	GET, POST /api/admin/settings
	POST /api/admin/logout


For front end,

	POST /api/frontend/login
	GET /api/frontend/dashboard
	POST /api/frontend/logout
