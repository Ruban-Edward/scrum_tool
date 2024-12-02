<?php
namespace Redmine\Config;

use CodeIgniter\Config\BaseService;
use Redmine\Services\IssuesService;
use Redmine\Services\UsersService;
use Redmine\Services\ProjectsService;
use Redmine\Services\TimeEntryService;
use Redmine\Services\CustomFieldService;
use Redmine\Services\CustomValueService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */

    public static function issues($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('issues');
        }

        return new IssuesService();
    }

    public static function customField($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('customField');
        }

        return new CustomFieldService();
    }

    public static function customValue($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('customValue');
        }

        return new CustomValueService();
    }

    public static function users($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('users');
        }

        return new UsersService();
    }

    public static function projects($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('projects');
        }

        return new ProjectsService();
    }

    public static function projectuser($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('projectuser');
        }

        return new ProjectsService();
    }

    public static function user($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('user');
        }

        return new UsersService();
    }

    public static function timeEntry($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('timeEntry');
        }

        return new TimeEntryService();
    }
}