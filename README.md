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

To alter table column

	php artisan make:migration add_logo_and_favicon_to_settings_table

File
	database/migrations/xxxx_add_logo_and_favicon_to_settings_table.php

	<?php
	
	use Illuminate\Database\Migrations\Migration;
	use Illuminate\Database\Schema\Blueprint;
	use Illuminate\Support\Facades\Schema;
	
	return new class extends Migration {
	    public function up(): void
	    {
	        Schema::table('settings', function (Blueprint $table) {
	            $table->text('site_logo')->nullable()->after('site_name');
	            $table->text('site_favicon')->nullable()->after('site_logo');
	        });
	    }
	
	    public function down(): void
	    {
	        Schema::table('settings', function (Blueprint $table) {
	            $table->dropColumn(['site_logo', 'site_favicon']);
	        });
	    }
	};

	php artisan migrate

app/Models/Setting.php

	protected $fillable = [
	    'site_name',
	    'facebook_link',
	    'twitter_link',
	    'site_logo',
	    'site_favicon',
	];

For validation Admin/SettingsController.php

	$request->validate([
	    'site_name'     => 'nullable|string|max:255',
	    'facebook_link' => 'nullable|url|max:255',
	    'twitter_link'  => 'nullable|url|max:255',
	    'site_logo'     => 'nullable|string',
	    'site_favicon'  => 'nullable|string',
	]);

To update,

	$settings->update($request->only([
	    'site_name',
	    'facebook_link',
	    'twitter_link',
	    'site_logo',
	    'site_favicon'
	]));
