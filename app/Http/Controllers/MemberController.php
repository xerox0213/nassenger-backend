<?php

namespace App\Http\Controllers;

use App\Http\Resources\MemberResource;
use App\Http\Services\MemberService;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(MemberService $memberService, Request $request)
    {
        $fullName = $request->get('full_name');

        $members = $memberService->index($fullName);

        return MemberResource::collection($members);
    }
}
