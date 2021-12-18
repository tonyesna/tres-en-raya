<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{

    private $move_computer = "";
    const  WIN_PLAYER_1 = ["X", "X", "X"];
    const  WIN_PLAYER_2 = ["O", "O", "O"];

    /**
     * @param int $index
     * @return Application|Factory|View
     */
    public function index(int $index = 1)
    {
        try{

            $game_model = $this->getGameModel();
            $exist_game_active=$game_model->where('state',config('app.start_state'))->where('type_game', '=', $index)->first();

            $board_size = config('app.n_x_n');

            if ($exist_game_active) {

                $board = json_decode($exist_game_active->getAttribute('board_game'), true);
                $data = ['size' => $board_size, 'board' => $board, 'turn' => $exist_game_active->getAttribute('turn'), 'type_game' => $index];

            } else {
                $new_board = $this->generateNewBoardGame($index);
                $data = ['size' => $board_size, 'board' => $new_board, 'turn' => 'player 1', 'type_game' => $index];

            }
        }catch (\Exception $ex){
            dd("ERROR", $ex);
        }


        return view("startGame")->with($data);

    }


    /**
     * @param Request $request
     * @return void
     */
    public function newGame(Request $request): void
    {
        $input = $request->all();
        $type_game = $input['type_game'];

        $game_model = $this->getGameModel();
        $exist_game_active=$game_model->where('state', config('app.start_state'))->where('type_game', '=', $type_game)->first();

         if ($exist_game_active) {

            $game_model->where('state', config('app.start_state'))->where('type_game', '=', $type_game)->update(
                ['state' => config('app.finish_state')]);
        }

        $this->index();
    }

    /**
     * @return Application|Factory|View
     */
    public function typeGame()
    {
        $type_game=1;
        if(isset($_GET['id'])){
            $type_game = $_GET['id'];
        }


        return $this->index($type_game);
    }


    /**
     * @param int $index
     * @return array
     */
    private function generateNewBoardGame(int $index): array
    {
        $new_board = array_fill(0, 9, '');

        $values = array('board_game' => json_encode($new_board),
            'turn' => config('app.player_1'),
            'type_game' => $index,
            'state' => config('app.start_state')

        );
       DB::table('boardgame')->insert($values);

        return $new_board;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function saveGameState(Request $request): JsonResponse
    {
        $input = $request->all();
        $state = config('app.start_state');
        $type_game = $input['type_game'];
        $position = $input['position'];
        $turn = $input['turn'];

        $board = $this->getBoardFromDatabase($type_game);

        if ($turn === config('app.player_1')) {
            $board[$position] = "X";
            $turn = config('app.player_2');
        } else {
            $board[$position] = "O";
            $turn = config('app.player_1');
        }

        $winner = $this->thereIsAWinner($board);

        if (!in_array("", $board)) {
            $state = 1;
        }

        if (!empty($winner)) {
            $state = 1;
        }

        if ($type_game == config('app.type_game_vs_pc') && $state != config('app.finish_state')) {
            $board = $this->moveComputer($board);
            $winner = $this->thereIsAWinner($board);

            if (!in_array("", $board)) {
                $state = config('app.finish_state');
            }

            if (!empty($winner)) {
                $state = config('app.finish_state');
            }
            $turn = config('app.player_1');
        }

        $data = ['turn' => $turn, 'state' => $state, 'winner' => $winner, 'type_game' => $type_game, 'move_computer' => $this->move_computer];

        DB::table('boardgame')->where('state', config('app.start_state'))->where('type_game', '=', $type_game)->update(
            ['board_game' => $board, 'turn' => $turn, 'state' => $state, 'type_game' => $type_game]);


        return response()->json(['success' => 'Got Simple Ajax Request.', 'data' => $data]);

    }

    /**
     * @param array $board
     * @return array
     */
    private function moveComputer(array $board): array
    {

        $empty_board_position = $this->getEmptyPosition($board);

        shuffle($empty_board_position);
        $this->move_computer = $empty_board_position[0];
        $board[$this->move_computer] = "O";

        return $board;

    }

    /**
     * @param array $board
     * @return array
     */
    private function getEmptyPosition(array $board): array
    {
        $empty_board_position = [];
        foreach ($board as $key => $value) {
            if (empty($value)) {
                $empty_board_position[] = $key;
            }
        }

        return $empty_board_position;

    }

    /**
     * @param int $type_game
     * @return array
     */
    private function getBoardFromDatabase(int $type_game): array
    {
        $exist_game_active = DB::table('boardgame')->where('state', config('app.start_state'))
            ->where('type_game', $type_game)->first();
        return json_decode($exist_game_active->board_game, true);
    }

    /**
     * @param array $board
     * @return array
     */
    private function thereIsAWinner(array $board): array
    {
        $thereIsWinner = $this->checkRow($board);
        if (!empty($thereIsWinner)) {
            return $thereIsWinner;
        }
        $thereIsWinner = $this->checkColum($board);
        if (!empty($thereIsWinner)) {
            return $thereIsWinner;
        }
        return $this->checkDiagonal($board);

    }

    /**
     * @param array $board
     * @return array
     */
    private function checkRow(array $board): array
    {

        $matrix_board = array_chunk($board, config('app.n_x_n'));
        $winner_row = [];
        for ($i = 0; $i < sizeof($matrix_board); $i++) {
            if ($matrix_board[$i] === self::WIN_PLAYER_1) {
                $winner_row = ["combination" => [($i * sizeof($matrix_board)), ($i * sizeof($matrix_board)) + 1, ($i * sizeof($matrix_board)) + 2], 'player' => config('app.player_1')];
            }
            if ($matrix_board[$i] === self::WIN_PLAYER_2) {
                $winner_row = ["combination" => [($i * sizeof($matrix_board)), ($i * sizeof($matrix_board)) + 1, ($i * sizeof($matrix_board)) + 2], 'player' => config('app.player_2')];
            }
        }

        return $winner_row;
    }

    /**
     * @param array $board
     * @return array
     */
    private function checkColum(array $board): array
    {
        $matrix_board = array_chunk($board, config('app.n_x_n'));
        $winner_column = [];

        for ($i = 0; $i < sizeof($matrix_board); $i++) {
            if (array_column($matrix_board, $i) === self::WIN_PLAYER_1) {
                $winner_column = ["combination" => [($i), ($i + 3), ($i + 6)], 'player' => config('app.player_1')];
            }
            if (array_column($matrix_board, $i) ===  self::WIN_PLAYER_2) {
                $winner_column = ["combination" => [($i), ($i + 3), ($i + 6)], 'player' => config('app.player_2')];
            }
        }

        return $winner_column;
    }

    /**
     * @param array $board
     * @return array
     */
    private function checkDiagonal(array $board): array
    {



        $diagonal_first = [$board[0], $board[4], $board[8]];

        $diagonal_second = [$board[2], $board[4], $board[6]];


        $matrix_diagonal = [$diagonal_first, $diagonal_second];


        $winner_column = [];

        for ($i = 0; $i < sizeof($matrix_diagonal); $i++) {
            if ($matrix_diagonal[$i] === self::WIN_PLAYER_1) {
                $combination = $this->getDiagonalCombinations($i);
                $winner_column = ["combination" => $combination, 'player' => config('app.player_1')];
            }
            if ($matrix_diagonal[$i] === self::WIN_PLAYER_2) {
                $combination = $this->getDiagonalCombinations($i);
                $winner_column = ["combination" => $combination, 'player' => config('app.player_2')];
            }
        }

        return $winner_column;

    }

    /**
     * @param int $index
     * @return array|int[]
     */
    private function getDiagonalCombinations(int $index): array
    {
        $combination = [];

        if ($index == 0) {
            $combination = [0, 4, 8];
        }
        if ($index == 1) {
            $combination = [2, 4, 6];
        }

        return $combination;
    }

    public function getGameModel(){
        return resolve(Game::class);
    }
}
