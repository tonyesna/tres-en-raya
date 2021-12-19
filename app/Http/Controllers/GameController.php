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
     * Method that searches for an existing game board or creates a new one depending on the type of game.
     * @param int $index
     * @return Application|Factory|View
     */
    public function index(int $index = 1)
    {
        try {

            $game_model = $this->getGameModel();
            $exist_game_active = $game_model->where('state', config('app.start_state'))->where('type_game', '=', $index)->first();

            $board_size = config('app.n_x_n');

            if ($exist_game_active) {

                $board = json_decode($exist_game_active->getAttribute('board_game'), true);
                $data = ['size' => $board_size, 'board' => $board, 'turn' => $exist_game_active->getAttribute('turn'), 'type_game' => $index];

            } else {
                $new_board = $this->generateNewBoardGame($index);
                $data = ['size' => $board_size, 'board' => $new_board, 'turn' => 'player 1', 'type_game' => $index];

            }
            return view("startGame")->with($data);
        } catch (\Exception $ex) {
            dd("ERROR ", "LINE: ", __LINE__, " METHOD:", __METHOD__, $ex);
        }
    }


    /**
     * Method that creates a new set for a specific type.
     * @param Request $request
     * @return void
     */
    public function newGame(Request $request): void
    {
        try {
            $input = $request->all();
            $type_game = $input['type_game'];

            $game_model = $this->getGameModel();
            $exist_game_active = $game_model->where('state', config('app.start_state'))->where('type_game', '=', $type_game)->first();

            if ($exist_game_active) {

                $game_model->where('state', config('app.start_state'))->where('type_game', '=', $type_game)->update(
                    ['state' => config('app.finish_state')]);
            }
            $this->index();
        } catch (\Exception $ex) {
            dd("ERROR ", "LINE: ", __LINE__, " METHOD:", __METHOD__, $ex);
        }
    }

    /**
     * Method that returns the type of game passed by parameter
     * @return Application|Factory|View
     */
    public function typeGame()
    {
        try {
            $type_game = 1;
            if (isset($_GET['id'])) {
                $type_game = $_GET['id'];
            }
            return $this->index($type_game);
        } catch (\Exception $ex) {
            dd("ERROR ", "LINE: ", __LINE__, " METHOD:", __METHOD__, $ex);
        }

    }


    /**
     *  Method that generates an empty board
     * @param int $index
     * @return array
     */
    public function generateNewBoardGame(int $index): array
    {
        try {
            $new_board = array_fill(0, 9, '');

            $values = array('board_game' => json_encode($new_board),
                'turn' => config('app.player_1'),
                'type_game' => $index,
                'state' => config('app.start_state')

            );
            DB::table('boardgame')->insert($values);
            return $new_board;
        } catch (\Exception $ex) {
            dd("ERROR ", "LINE: ", __LINE__, " METHOD:", __METHOD__, $ex);
        }
    }

    /**
     * Method that keeps track of the status of each move
     * @param Request $request
     * @return JsonResponse
     */
    public function saveGameState(Request $request): JsonResponse
    {
        try {
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

            $winner = $this->boardIfThereIsAWinner($board);

            if (!in_array("", $board)) {
                $state = 1;
            }

            if (!empty($winner)) {
                $state = 1;
            }

            if ($type_game == config('app.type_game_vs_pc') && $state != config('app.finish_state')) {
                $board = $this->moveComputer($board);
                $winner = $this->boardIfThereIsAWinner($board);

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


            return response()->json(['success' => 'OK', 'data' => $data]);
        } catch (\Exception $ex) {
            dd("ERROR ", "LINE: ", __LINE__, " METHOD:", __METHOD__, $ex);
        }
    }

    /**
     * Method that returns the movement performed by the computer.
     * @param array $board
     * @return array
     */
    public function moveComputer(array $board): array
    {

        try {
            $empty_board_position = $this->getEmptyPosition($board);

            shuffle($empty_board_position);
            $this->move_computer = $empty_board_position[0];
            $board[$this->move_computer] = "O";

            return $board;
        } catch (\Exception $ex) {
            dd("ERROR ", "LINE: ", __LINE__, " METHOD:", __METHOD__, $ex);
        }


    }

    /**
     * Method that checks for voids
     * @param array $board
     * @return array
     */
    public function getEmptyPosition(array $board): array
    {
        try {
            $empty_board_position = [];
            foreach ($board as $key => $value) {
                if (empty($value)) {
                    $empty_board_position[] = $key;
                }
            }

            return $empty_board_position;
        } catch (\Exception $ex) {
            dd("ERROR ", "LINE: ", __LINE__, " METHOD:", __METHOD__, $ex);
        }


    }

    /**
     * Method retrieving a database dashboard
     * @param int $type_game
     * @return array
     */
    private function getBoardFromDatabase(int $type_game): array
    {
        try {
            $exist_game_active = DB::table('boardgame')->where('state', config('app.start_state'))
                ->where('type_game', $type_game)->first();
            return json_decode($exist_game_active->board_game, true);
        } catch (\Exception $ex) {
            dd("ERROR ", "LINE: ", __LINE__, " METHOD:", __METHOD__, $ex);
        }

    }

    /**
     * A method that recovers a board if there is a winner.
     * @param array $board
     * @return array
     */
    public function boardIfThereIsAWinner(array $board): array
    {
        try {
            $thereIsWinner = $this->checkRow($board);
            if (!empty($thereIsWinner)) {
                return $thereIsWinner;
            }
            $thereIsWinner = $this->checkColum($board);
            if (!empty($thereIsWinner)) {
                return $thereIsWinner;
            }
            return $this->checkDiagonal($board);
        } catch (\Exception $ex) {
            dd("ERROR ", "LINE: ", __LINE__, " METHOD:", __METHOD__, $ex);
        }


    }

    /**
     * Method that checks the row
     * @param array $board
     * @return array
     */
    public function checkRow(array $board): array
    {

        try {
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
        } catch (\Exception $ex) {
            dd("ERROR ", "LINE: ", __LINE__, " METHOD:", __METHOD__, $ex);
        }

    }

    /**
     * Method that checks the column
     * @param array $board
     * @return array
     */
    public function checkColum(array $board): array
    {
        try {
            $matrix_board = array_chunk($board, config('app.n_x_n'));
            $winner_column = [];

            for ($i = 0; $i < sizeof($matrix_board); $i++) {
                if (array_column($matrix_board, $i) === self::WIN_PLAYER_1) {
                    $winner_column = ["combination" => [($i), ($i + 3), ($i + 6)], 'player' => config('app.player_1')];
                }
                if (array_column($matrix_board, $i) === self::WIN_PLAYER_2) {
                    $winner_column = ["combination" => [($i), ($i + 3), ($i + 6)], 'player' => config('app.player_2')];
                }
            }

            return $winner_column;
        } catch (\Exception $ex) {
            dd("ERROR ", "LINE: ", __LINE__, " METHOD:", __METHOD__, $ex);
        }

    }

    /**
     * Method that checks the diagonal
     * @param array $board
     * @return array
     */
    public function checkDiagonal(array $board): array
    {

        try {
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
        } catch (\Exception $ex) {
            dd("ERROR ", "LINE: ", __LINE__, " METHOD:", __METHOD__, $ex);
        }


    }

    /**
     * Method that tests the possible combinations
     * @param int $index
     * @return array|int[]
     */
    public function getDiagonalCombinations(int $index): array
    {
        try {
            $combination = [];

            if ($index == 0) {
                $combination = [0, 4, 8];
            }
            if ($index == 1) {
                $combination = [2, 4, 6];
            }

            return $combination;
        } catch (\Exception $ex) {
            dd("ERROR ", "LINE: ", __LINE__, " METHOD:", __METHOD__, $ex);
        }

    }

    /**
     * Method that returns a Game model
     * @return Game
     */
    public function getGameModel(): Game
    {
        try {
            return resolve(Game::class);
        } catch (\Exception $ex) {
            dd("ERROR ", "LINE: ", __LINE__, " METHOD:", __METHOD__, $ex);
        }
    }
}
