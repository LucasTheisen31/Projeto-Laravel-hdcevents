<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//incluimos os Controllers utilizados
use App\Http\Controllers\EventController;
use App\Http\Controllers\ContactController;

Route::get('/', [EventController::class, 'index']);//leva a tela home

Route::get('/events/create', [EventController::class, 'create'])->middleware('auth');//leva a tela de criar evento, desde que tenha um usuario logado

Route::get('/events/{id}', [EventController::class, 'show']);//leva a tela para visualizar os detalhes de um evento

Route::post('/events', [EventController::class, 'store']); //salva um dado no banco

Route::delete('events/{id}', [EventController::class, 'destroy'])->middleware('auth'); //rota para deletar um evento passando o id do mesmo

Route::get('/dashboard', [EventController::class, 'dashboard'])->middleware('auth');//leva a tela dashboard, utiliza o metodo 'dashboard' do controler 'EventController' e so permite se tiver um usuario logado.

Route::get('/events/edit/{id}', [EventController::class, 'edit'])->middleware('auth');//chama o método edit do EventController, o qual vai buscar o evento do id e retornar a tela de edição (Somente para poder ver e editar os dados do evento)

Route::put('/events/update/{id}', [EventController::class, 'update'])->middleware('auth');//chama o método update do EventController, o qual salva os dados editados do evento no banco

Route::post('/events/join/{id}', [EventController::class, 'joinEvent'])->middleware('auth');//chama o método joinEvent do EventController, o qual inscreve o usuario em um evento

Route::delete('/events/leave/{id}', [EventController::class, 'leaveEvent'])->middleware('auth');//chama o método leaveEvent do EventController, o qual cancela a inscrição do usuario de um evento

Route::get('/contact', [ContactController::class, 'teste']);
