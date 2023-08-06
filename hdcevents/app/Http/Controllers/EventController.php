<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//Informa que uso o model Event e o model User
use App\Models\Event;
use App\Models\User;

class EventController extends Controller
{
    //retorna a tela welcome
    public function index(){

        $search = request('search');

        if ($search) {
            $events = Event::where([
                ['title', 'like', '%'.$search.'%']//se contem a palavra pesquisada no titulo
            ])->get();

        } else {
            //chamamos o comando o ORM "all" para pegarmos todos os eventos cadastrados no banco
            $events = Event::all();
        }

        //Retornamos a view 'welcome' e passamos os paramentos da lista de eventos e o search
        return view('welcome', ['events'=>$events, 'search'=>$search]);
    }

    //retorna a tela create
    public function create(){
        return view('events.create');
    }

    //salva um novo evento no banco de dados
    public function store(Request $request){
        //instanciamos um objeto "model" Event
        $event = new Event;

        $event->title = $request->title;
        $event->date = $request->date;
        $event->description = $request->description;
        $event->city = $request->city;
        $event->private = $request->private;
        $event->items = $request->items; //array de items, no model Event definimos um cast para o atributo items informando que é um array

        //Image upload
        if($request->hasFile('image') && $request->file('image')->isValid()) {
            $requestImage = $request->image;

            $extension = $requestImage->extension();
            //geramos um nome unico para o arquivo da imagem
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;

            //A imagem é salva no servidor e o nome da imagem é salvo no banco de dados
            //salvamos a imagem no servidor no diretorio 'img/events', com o nome de 'imageName'
            $requestImage->move(public_path('img/events'), $imageName);

            //passamos para o model o nome da imagem que sera salvo no banco
            $event->image = $imageName;

        }

        //pegamos o usuario logado que esta criando o evento
        $user = auth()->user();
        $event->user_id = $user->id;

        $event->save();

        //redireciona para a tela inicial, retorna uma mensagem para
        //acessarmos atravez de flash massage na view "main.php"
        //with envia uma mensagem de sessão para a view
        return redirect('/')->with('msg', 'Evento criado com sucesso!');
    }

    //Busca um evento no banco de dados. O parâmetro $id representa o ID do evento ao qual o usuário deseja visualizar.
    public function show($id){
        //Pega o usuario autenticado (logado)
        $user = auth()->user();
        //variavel para controlar se o usuario ja esta participando do evento eu não. Para ele não poder se inscrever
        //mais de uma vez no mesmo evento
        $hasUserJoined = false;
        //se tiver um usuario logado
        if($user){
            //recupera os eventos que o usuario esta participando
            $userEvents = $user->eventsAsParticipant->toArray();
            //passa pelos eventos do usuario verificando se ele ja esta inscrito
            foreach($userEvents as $userEvent){
                if($userEvent['id'] == $id){
                    $hasUserJoined = true;
                }
            }
        }
        $event = Event::findOrFail($id);//busca um evento pelo id

        //busca o usuario que possui o id igual ao que esta vinculado no evento na coluna 'user_id'
        $eventOwner = User::where('id', $event->user_id)->first()->toArray();

        //retorna a view 'show' com o dado do evento, o dono 'criador' do evneto, e se o usuario atual já esta inscrito no evento ou não
        return view('events.show', ['event' => $event, 'eventOwner'=> $eventOwner, 'hasUserJoined'=>$hasUserJoined]);
    }

    public function dashboard(){
        //pegamos o usuario logado
        $user = Auth()->user();

        //pega os eventos do usuario. O model User já possui a lista de eventos do usuario
        $events = $user->events;

        //pega os eventos que o usuario participa.
        $eventsAsParticipant = $user->eventsAsParticipant;

        //retorna a view 'dashboard' e os eventos que o usuairo é dono e os eventos que o usuario participa
        return view('events.dashboard', ['events' => $events, 'eventsAsParticipant' => $eventsAsParticipant]);
    }

    public function destroy($id){
        //buscamos o evento e deletamos
        Event::findOrFail($id)->delete();

        //redireciona para a view 'dashboard'.
        //Além disso, envia uma mensagem flash (mensagem temporária que será exibida na próxima solicitação) com a mensagem 'Evento excluído com sucesso!'
        return redirect('/dashboard')->with('msg', 'Evento excluído com sucesso!');
    }

    //Método que busca o evendo que possui o id passado, e retorna a view de edição
    public function edit($id){
        //pega o usuario logado
        $user = auth()->user();

        //busca o evento no banco
        $event = Event::findOrFail($id);

        //se o usuario que esta tentando editar o evento não é o dono do evento
        if($user->id != $event->user_id){
            //redireciona para a dashboard para não deixar um usuario editar um evento que não é seu
            return redirect('/dashboard');
        }

        //retorna a view com o evento como parametro
        return view('events.edit', ['event' => $event]);
    }

    //Método que atualiza os dados de um evento
    public function update(Request $request)
    {
        //Retorna o registro do evento correspondente ao ID fornecido, ou lança uma exceção caso não encontre o evento.
        $event = Event::findOrFail($request->id);
        $data = $request->all();

        //Verifica se existe um arquivo de imagem na solicitação e se esse arquivo é válido
        if ($request->hasFile('image') && $request->file('image')->isValid()) {

            //Exclui a imagem antiga do evento. Isso é feito com a função unlink, que exclui o arquivo de imagem localizado na pasta "public/img/events".
            unlink(public_path('img/events/' . $event->image));

            //Recupera o novo arquivo de imagem do objeto "Request" e gera um nome único para ele
            $requestImage = $request->image;
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;

            //Move o novo arquivo de imagem para a pasta "public/img/events" no servidor.
            $requestImage->move(public_path('img/events'), $imageName);

            //Atualiza a entrada "image" na variável "data" para armazenar o nome do novo arquivo de imagem gerado.
            $data['image'] = $imageName;
        }
        Event::findOrFail($request->id)->update($data);

        //redireciona para a view 'dashboard'.
        //Além disso, envia uma mensagem flash (mensagem temporária que será exibida na próxima solicitação) com a mensagem 'Evento atualizado com sucesso!'
        return redirect('/dashboard')->with('msg', 'Evento editado com sucesso!');
    }

    //metodo que inscreve um usuario em um evento. O parâmetro $id representa o ID do evento ao qual o usuário deseja se inscrever.
    public function joinEvent($id){
        //Pega o usuario autenticado (logado)
        $user = auth()->user();

        //Nesta linha, o código está vinculando o ID do evento no atributo eventAsParticipant do usuário.
        //O método attach() é usado para adicionar um evento à lista de eventos que o usuário participa.
        $user->eventsAsParticipant()->attach($id);

        //Recupera o objeto do evento correspondente ao ID fornecido
        $event = Event::findOrFail($id);

        //Redireciona o usuário para a página '/dashboard' após a inscrição no evento.
        //Além disso, envia uma mensagem flash (mensagem temporária que será exibida na próxima solicitação) informando ao usuário que sua presença foi confirmada no evento
        return redirect('/dashboard')->with('msg', 'Sua presença está confirmada no evento ' . $event->title);
    }

    //metodo que cancela a inscrição do usuario de um evento. O parâmetro $id representa o ID do evento ao qual o usuário deseja cancelar sua inscrição.
    public function leaveEvent($id){
        //Pega o usuario autenticado (logado)
        $user = auth()->user();

        //Nesta linha, o código está desvinculando o ID do evento do atributo eventsAsParticipant do usuário.
        //O método detach() é usado para remover o evento da lista de eventos aos quais o usuário estava inscrito.
        $user->eventsAsParticipant()->detach($id);

        //Recupera o objeto do evento correspondente ao ID fornecido
        $event = Event::findOrFail($id);

        //Redireciona o usuário para a página '/dashboard' após a remover a inscrição do evento.
        //Além disso, envia uma mensagem flash (mensagem temporária que será exibida na próxima solicitação) informando ao usuário que ele saiu com sucesso do evento.
        return redirect('/dashboard')->with('msg', 'Você saiu com sucesso do evento ' . $event->title);
    }
}
