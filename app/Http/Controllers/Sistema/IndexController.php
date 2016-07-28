<?php

namespace App\Http\Controllers\Sistema;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function index()
    {
        if(!Auth::guest())
            return redirect()->route('produto.index');
        return view('content.sistema.index');
    }

    public function store(Requests\Sistema\AuthRequest $request)
    {
        if(Auth::attempt($request->only(['email', 'password'])))
            return redirect()->route('produto.index');
        return redirect()->back()->withErrors(['email' => 'Email e senha não conferem']);
    }

    public function destroy()
    {
        Auth::logout();
        return redirect()->route('sistema.index')->with(['success' => 'Você saiu do sistema']);
    }
}
