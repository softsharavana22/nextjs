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
