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


In routes/adminapi.php

	use Illuminate\Http\Request;

	Route::middleware(['auth:sanctum', 'admin.auth'])->group(function () {
	        // ✅ TOKEN VALIDATION API (Heartbeat)
	        Route::get('/current-time', function (Request $request) {
	            return response()->json([
	                'status' => 200,
	                'time'   => now()->toDateTimeString(),
	                'user'   => auth()->user(), // ✅ always works // $request->user(), // ✅ works
	            ]);
	        });
	 });


In login api,

	public function login(Request $request)
	{
		$request->validate([
			'email' => 'required|email',
			'password' => 'required',
			'pattern' => 'required|digits:5'
		]);
	
		$admin = AdminUser::where('email', $request->email)->first();
	
		if (
			!$admin ||
			!Hash::check($request->password, $admin->password) ||
			$admin->pattern !== $request->pattern
		) {
			return response()->json([
				'message' => 'Invalid credentials'
			], 401);
		}
	
		// 🔥 REVOKE ALL OLD TOKENS
		$admin->tokens()->delete();        
	
		$token = $admin->createToken('admin-token', ['admin'])->plainTextToken;
	
		return response()->json([
			'token' => $token,
			'admin' => $admin
		]);
	}
		------------------------------------------------

Job
	
	php artisan queue:table
	php artisan migrate

phpMyAdmin tables:

	jobs
	failed_jobs

Create Normal Job

	php artisan make:job SendEmailJob

app/Jobs/SendEmailJob.php

	<?php
	
	namespace App\Jobs;
	
	use App\Models\User;
	use App\Models\CronCheck;
	use Illuminate\Bus\Queueable;
	use Illuminate\Contracts\Queue\ShouldQueue;
	use Illuminate\Foundation\Bus\Dispatchable;
	use Illuminate\Queue\InteractsWithQueue;
	use Illuminate\Queue\SerializesModels;
	use Illuminate\Support\Facades\Cache;
	use Illuminate\Support\Facades\Log;
	use DB;
	
	class SendEmailJob implements ShouldQueue
	{
	    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	
	    public function __construct(
	        public int $userId
	    ) {}
	
	    public function handle(): void
	    {
	        /**
	         * 1️⃣ Prevent duplicate execution (CRON lock)
	         */
	        $lock = Cache::lock('send-email-job-lock', 60); // 5 minutes
	
	        if (! $lock->get()) {
	            return; // already running somewhere else
	        }
	
	        try {
	            /**
	             * 2️⃣ Your actual CRON logic
	             */
	            $user = User::find($this->userId);
	            // echo '<pre>';
	            // print_r($user);
	
	            if (! $user) {
	                Log::warning('User not found, stopping recurring job');
	                return;
	            }
	
	            Log::info("Running every 5 min job for {$user->email}");
	
	            //$check = DB::table('cron_checks')->where('id', 1)->first();
	            $check = DB::table('cron_checks')->orderBy('id', 'desc')->first();
	            if(!empty($check)) {
	                $data = $check->id + 1;
	                $insertData['name'] = $data;
	                DB::table('cron_checks')->insert($insertData);                
	            }
	            else {
	                $insertData['name'] = 'test';
	                DB::table('cron_checks')->insert($insertData);
	            }
	
	            // 👉 YOUR TASK HERE
	            // send email
	            // cleanup
	            // sync data
	            // etc.
	
	        } finally {
	            $lock->release();
	        }
	
	        /**
	         * 3️⃣ THIS IS THE CRON PART (VERY IMPORTANT)
	         * Re-dispatch the SAME job after 5 minutes
	         */
	        self::dispatch($this->userId)->delay(now()->addMinutes(1));
	    }
	}

app/Http/Controllers/CronController.php

	<?php
	
	namespace App\Http\Controllers;
	
	use App\Jobs\SendEmailJob;
	
	class CronController extends Controller
	{
	    public function start()
	    {
	        // example user id
	        $userId = auth()->id() ?? 1;
	
	        SendEmailJob::dispatch($userId);
	
	        return 'Recurring job started';
	    }
	}

routes/web.php

	Route::get('/start-cron-job', [CronController::class, 'start']);

	php artisan cron:start

	php artisan queue:work

Steps:
Step 1 — Create a batch file

Open Notepad.

Paste this:

	cd /d D:\xampp\htdocs\laravelapi
	php artisan queue:work --tries=3 --timeout=300


	Replace D:\xampp\htdocs\laravelapi with your Laravel project path.

Save it as:

	start-laravel-queue.bat

Step 2 — Open Task Scheduler

	Press Windows + R, type taskschd.msc, hit Enter.
	
	Click Create Task.
	
	Name it: Laravel Queue Worker.
	
	Check Run whether user is logged on or not.
	
	Go to Triggers → New → At startup.
	
	Go to Actions → New → Start a program.
	
	Program/script: D:\path\to\start-laravel-queue.bat
	
	Go to Conditions and uncheck Stop if computer switches to battery power (optional).
	
	Save.

