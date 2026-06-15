<?php

namespace App\Enums;

enum Permission: string
{
    // Company Management
    case VIEW_COMPANIES = 'view_companies';
    case CREATE_COMPANIES = 'create_companies';
    case UPDATE_COMPANIES = 'update_companies';
    case DELETE_COMPANIES = 'delete_companies';

    // Internship Management
    case VIEW_INTERNSHIPS = 'view_internships';
    case CREATE_INTERNSHIPS = 'create_internships';
    case UPDATE_INTERNSHIPS = 'update_internships';
    case DELETE_INTERNSHIPS = 'delete_internships';

    // User Management
    case VIEW_USERS = 'view_users';
    case MANAGE_USERS = 'manage_users';

    // Security & Settings
    case VIEW_LOGS = 'view_logs';
    case MANAGE_SETTINGS = 'manage_settings';
}
