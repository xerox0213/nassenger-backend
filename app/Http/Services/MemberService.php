<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class MemberService
{
    public function index(string|null $fullName)
    {
        $concat = DB::raw("concat(firstname, ' ', lastname)");
        $value = $fullName ? "%$fullName%" : "";

        return User::whereLike($concat, $value)->simplePaginate(10);
    }
}
