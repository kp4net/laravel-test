<?php

namespace Database\Seeders;

use App\Models\BusinessService;
use App\Models\MenuItem;
use App\Models\Workshop;
use App\Models\Event;
use App\Models\Leave;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\BookingTime;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */

    protected function seedMenu() {
        $rootItem = MenuItem::create([
            'name' => 'All events',
            'url' => '/events',
        ]);

        $laraconItem = MenuItem::create([
            'name' => 'Laracon',
            'url' => '/events/laracon',
            'parent_id' => $rootItem->id
        ]);

        MenuItem::create([
            'name' => 'Illuminate your knowledge of the laravel code base',
            'url' => '/events/laracon/workshops/illuminate',
            'parent_id' => $laraconItem->id
        ]);

        MenuItem::create([
            'name' => 'The new Eloquent - load more with less',
            'url' => '/events/laracon/workshops/eloquent',
            'parent_id' => $laraconItem->id
        ]);

        $reactconItem = MenuItem::create([
            'name' => 'Reactcon',
            'url' => '/events/reactcon',
            'parent_id' => $rootItem->id
        ]);

        MenuItem::create([
            'name' => '#NoClass pure functional programming',
            'url' => '/events/reactcon/workshops/noclass',
            'parent_id' => $reactconItem->id
        ]);

        MenuItem::create([
            'name' => 'Navigating the function jungle',
            'url' => '/events/reactcon/workshops/jungle',
            'parent_id' => $reactconItem->id
        ]);
    }

    protected function seedEvents() {
        $date = (new Carbon())->subYear()->setDay(21);

        $lcon1 = Event::create([
            'name' => 'Laravel convention '.$date->year
        ]);

        Workshop::create([
            'start' => $date->clone()->setMonth(2)->setHour(10),
            'end' => $date->clone()->setMonth(2)->setHour(16),
            'name' => 'Illuminate your knowledge of the laravel code base',
            'event_id' => $lcon1->id
        ]);

        $date = (new Carbon())->addYears(1);

        $lcon2 = Event::create([
            'name' => 'Laravel convention '.$date->year
        ]);

        Workshop::create([
            'start' => $date->clone()->setMonth(10)->setHour(10),
            'end' => $date->clone()->setMonth(10)->setHour(16),
            'name' => 'The new Eloquent - load more with less',
            'event_id' => $lcon2->id
        ]);

        Workshop::create([
            'start' => $date->clone()->setMonth(11)->setHour(10),
            'end' => $date->clone()->setMonth(11)->setHour(17),
            'name' => 'AutoEx - handles exceptions 100% automatic',
            'event_id' => $lcon2->id
        ]);

        $rcon = Event::create([
            'name' => 'React convention '.$date->year
        ]);

        Workshop::create([
            'start' => $date->clone()->setMonth(8)->setHour(10),
            'end' => $date->clone()->setMonth(8)->setHour(18),
            'name' => '#NoClass pure functional programming',
            'event_id' => $rcon->id
        ]);

        Workshop::create([
            'start' => $date->clone()->setMonth(11)->setHour(9),
            'end' => $date->clone()->setMonth(11)->setHour(17),
            'name' => 'Navigating the function jungle',
            'event_id' => $rcon->id
        ]);
    }

    protected function seedRoles() {
        $roles = [
            [
                'name' => 'superadmin',
                'display_name' => 'Super Admin',
                'description' => 'Super Admin',
            ],
            [
                'name' => 'employee',
                'display_name' => 'Employee',
                'description' => 'Employee',
            ],
            [
                'name' => 'customer',
                'display_name' => 'Customer',
                'description' => 'Customer',
            ]
        ];

        User::truncate();
        RoleUser::truncate();
        Role::truncate();
        foreach ($roles as $role) {
            Role::create($role);
        }
    }

    protected function seedUsers() {
        User::truncate();
        $user = new User();
        $user->name = 'Super Admin';
        $user->email = 'admin@example.com';
        $user->email_verified_at = date('Y-m-d H:i:s');
        $user->password = '123456';

        $user->save();

        $user->attachRole(Role::select('id', 'name')->where('name', 'superadmin')->first()->id);

        $emp = new User();
        $emp->name = 'Employee';
        $emp->email = 'employee@example.com';
        $emp->email_verified_at = date('Y-m-d H:i:s');
        $emp->password = '123456';

        $emp->save();

        $emp->attachRole(Role::select('id', 'name')->where('name', 'employee')->first()->id);
    }

    protected function seedServices() {
        BusinessService::truncate();
        BusinessService::insert([
            [
                'name' => 'Men Haircut',
                'slug' => 'men-hair-cut',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '100',
                'time' => '30',
                'time_type' => 'minutes',
                'discount' => '10.00',
                'net_price' => '100.00',
                'image' => '["hair-cut.jpeg", "hair-spa.jpg"]',
                'default_image' => 'hair-cut.jpeg',
                'discount_type' => 'percent',
                'status' => 'active'
            ],
            [
                'name' => 'Women Haircut',
                'slug' => 'women-hair-cut',
                'description' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Est ultricies integer quis auctor elit. Velit scelerisque in dictum non consectetur a erat nam at. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Mauris pellentesque pulvinar pellentesque habitant morbi. Auctor elit sed vulputate mi sit amet mauris. Id nibh tortor id.</p>
                <br><p>Eu augue ut lectus arcu bibendum at vaes mi tempus imperdiet nulla malesuada pellentesque elit. Pellentesque id nibh tortor id aliquet lectus proin. Accumsan sit amet nulla facilisi morbi tempus iaculis urna id. Volutpat blandit aliquam etiam erat velit. Tempus egestas sed sed risus pretium quam vulputate dignissim.</p>',
                'price' => '150',
                'time' => '30',
                'time_type' => 'minutes',
                'discount' => '15.00',
                'net_price' => '150.00',
                'image' => '["hair-cut.jpeg", "hair-spa.jpg"]',
                'default_image' => 'hair-cut.jpeg',
                'discount_type' => 'percent',
                'status' => 'active'
            ]
        ]);
    }

    protected function seedLeave() {
        Leave::truncate();
        $leave = new Leave();
        $leave->user_id = 2;
        $leave->start_date = date('Y-m-d H:i:s', strtotime("+3 days"));
        $leave->end_date = date('Y-m-d H:i:s', strtotime("+3 days"));
        $leave->leave_type = 'public holiday';
        $leave->status = 'approved';
        $leave->save();
    }

    protected function seedSchedules() {
        BookingTime::truncate();
        $days = ["sunday","monday","tuesday","wednesday","thursday","friday","saturday"];
        foreach($days as $day) {
            $data[] = [
                'user_id' => 2,
                'day' => $day,
                'start_time' => $day == 'saturday' ? '10:00' : '22:00',
                'end_time' => $day == 'saturday' ? '08:00' : '20:00',
                'multiple_booking' => 'yes',
                'max_booking' => 3,
                'status' => $day == 'sunday' ? 'disabled' : 'enabled',
                'slot_duration' => 10
            ];
        }
        BookingTime::insert($data);

    }

    public function run()
    {
        DB::transaction(function($table) {
            DB::statement("SET foreign_key_checks=0");
            $this->seedEvents();
            $this->seedMenu();
            $this->seedRoles();
            $this->seedUsers();
            $this->seedServices();
            $this->seedLeave();
            $this->seedSchedules();
            DB::statement("SET foreign_key_checks=1");
        });
    }
}
