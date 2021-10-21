<?php // routes/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use Diglactic\Breadcrumbs\Breadcrumbs;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
});




Breadcrumbs::for('admin.admin_dashboard', function ($trail) {
    $trail->push('Dashboard', route('admin.admin_dashboard'));
});


// users 
Breadcrumbs::for('admin.users.index', function ($trail) {
	$trail->parent('admin.admin_dashboard');
    $trail->push('User List', route('admin.users.index'));
});
Breadcrumbs::for('admin.users.edit', function ($trail,$user) {
	$trail->parent('admin.admin_dashboard');
	$trail->push('Users', route('admin.users.index'));
    $trail->push($user->name, route('admin.users.edit',$user));
});
Breadcrumbs::for('admin.users.show', function ($trail,$user) {
	$trail->parent('admin.admin_dashboard');
	$trail->push('Users', route('admin.users.index'));
    $trail->push($user->name, route('admin.users.show',$user));
});
Breadcrumbs::for('admin.users.create', function ($trail) {
	$trail->parent('admin.admin_dashboard');
	$trail->push('Users', route('admin.users.index'));
    $trail->push('User Create', route('admin.users.create'));
});


// permissions 
Breadcrumbs::for('admin.permissions.index', function ($trail) {
	$trail->parent('admin.admin_dashboard');
    $trail->push('Permissions List', route('admin.permissions.index'));
});
Breadcrumbs::for('admin.permissions.edit', function ($trail,$permissions) {
	$trail->parent('admin.admin_dashboard');
	$trail->push('permissions', route('admin.permissions.index'));
    $trail->push($permissions->name, route('admin.permissions.edit',$permissions));
});
Breadcrumbs::for('admin.permissions.show', function ($trail,$permissions) {
	$trail->parent('admin.admin_dashboard');
	$trail->push('permissions', route('admin.permissions.index'));
    $trail->push($permissions->name, route('admin.permissions.show',$permissions));
});

Breadcrumbs::for('admin.permissions.create', function ($trail) {
	$trail->parent('admin.admin_dashboard');
	$trail->push('permissions', route('admin.permissions.index'));
    $trail->push('Create permissions', route('admin.permissions.create'));
});


// Roles 
Breadcrumbs::for('admin.roles.index', function ($trail) {
	$trail->parent('admin.admin_dashboard');
    $trail->push('roles List', route('admin.roles.index'));
});
Breadcrumbs::for('admin.roles.edit', function ($trail,$roles) {
	$trail->parent('admin.admin_dashboard');
	$trail->push('roles', route('admin.roles.index'));
    $trail->push($roles->name, route('admin.roles.edit',$roles));
});
Breadcrumbs::for('admin.roles.show', function ($trail,$roles) {
	$trail->parent('admin.admin_dashboard');
	$trail->push('roles', route('admin.roles.index'));
    $trail->push($roles->name, route('admin.roles.show',$roles));
});
Breadcrumbs::for('admin.roles.create', function ($trail) {
	$trail->parent('admin.admin_dashboard');
	$trail->push('roles', route('admin.roles.index'));
    $trail->push('Create Role', route('admin.roles.create'));
});
