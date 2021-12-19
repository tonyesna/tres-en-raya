<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\GameController;

use App\Models\Game;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class GameControllerTest extends TestCase
{
    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function test_typeGame()
    {
        $mock_game_controller = $this->getMockBuilder(GameController::class)
        ->onlyMethods(['index'])->getMock();
        $mock_game_controller->method('index')->willReturn(View::class);
        $return = $mock_game_controller->typeGame();

        self::assertEquals(View::class,$return);
    }

    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function test_generateNewBoardGame()
    {
        DB::shouldReceive('table')->once()->andReturnSelf();
        DB::shouldReceive('insert')->once()->andReturnSelf();

        $mock_game_controller = $this->getMockBuilder(GameController::class)
            ->onlyMethods([])->getMock();

        $return = $mock_game_controller->generateNewBoardGame(1);

        self::assertIsArray($return);
    }

    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function test_boardIfThereIsAWinner()
    {

        $mock_game_controller = $this->getMockBuilder(GameController::class)
            ->onlyMethods(['checkRow','checkColum','checkDiagonal'])->getMock();
        $mock_game_controller->method('checkRow')->willReturn([]);
        $mock_game_controller->method('checkColum')->willReturn([]);
        $mock_game_controller->method('checkDiagonal')->willReturn([]);

        $return = $mock_game_controller->boardIfThereIsAWinner([]);

        self::assertIsArray($return);
    }

    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function test_checkRow()
    {
        Config::set('app.n_x_n',3);
        $mock_game_controller = $this->getMockBuilder(GameController::class)
            ->onlyMethods([])->getMock();

        $return = $mock_game_controller->checkRow([]);

        self::assertIsArray($return);
    }

    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function test_checkDiagonal()
    {
        Config::set('app.player_1', 'player 1');
        Config::set('app.player_2', 'player 2');
        $board = array_fill(0, 9, '');
        $mock_game_controller = $this->getMockBuilder(GameController::class)
            ->onlyMethods(['getDiagonalCombinations'])->getMock();

        $mock_game_controller->method('getDiagonalCombinations')->with(1)->willReturn([]);
        $return = $mock_game_controller->checkDiagonal($board);

        self::assertIsArray($return);
    }

    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function test_checkColum()
    {
        Config::set('app.n_x_n',3);
        $mock_game_controller = $this->getMockBuilder(GameController::class)
            ->onlyMethods([])->getMock();

        $return = $mock_game_controller->checkColum([]);

        self::assertIsArray($return);
    }

    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function test_getEmptyPosition()
    {
        $mock_game_controller = $this->getMockBuilder(GameController::class)
            ->onlyMethods([])->getMock();

        $return = $mock_game_controller->getEmptyPosition([]);

        self::assertIsArray($return);
    }

    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function test_moveComputer()
    {
        $mock_game_controller = $this->getMockBuilder(GameController::class)
            ->onlyMethods(['getEmptyPosition'])->getMock();
        $mock_game_controller->method('getEmptyPosition')->with([])->willReturn(['','']);

        $return = $mock_game_controller->moveComputer([]);

        self::assertIsArray($return);
    }

    /**
     * A basic unit test example.
     * @test
     * @return void
     */
    public function test_getGameModel()
    {
        $mock_game_controller = $this->getMockBuilder(GameController::class)
            ->onlyMethods([])->getMock();
        $return = $mock_game_controller->getGameModel();

        self::assertInstanceOf(Game::class,$return);
    }
}
