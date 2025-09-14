<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Inertia\Inertia;
use App\Http\Requests\Admin\User\UserIndexRequest;

class UserController extends Controller
{
    public function index(UserIndexRequest $request)
    {
        $filters = $request->validated();
        $perPage = $filters['per_page'] ?? 10;

        $users = User::query()
            ->applyFilters($filters)
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'perPageOptions' => UserIndexRequest::PER_PAGE_OPTIONS,
            'filters' => $filters,
        ]);
    }
}
