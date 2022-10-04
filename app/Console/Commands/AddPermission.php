<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\OurEdu\Roles\Role;

class AddPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:addPermission {module} {permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'adding permission';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $permission = $this->argument('permission').'-'.$this->argument('module');
        $roles = Role::all();
        foreach($roles as $role){
            $role_permissions = $role->permissions;
            if(!in_array($permission,$role_permissions)){
                $role_permissions[]=$permission;
                $role->update(['permissions'=>$role_permissions]);
            }
        }
        return 0;
    }
}
