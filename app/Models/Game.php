<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    public $table = "boardgame";
    /**
     * @var array
     */
    public  $board_game;

    /**
     * @var string
     */
    public  $turn;

    /**
     * @var int
     */
    public  $type_game;

    /**
     * @var boolean
     */
    public  $state;

    /**
     * @return array
     */
    public function getBoardGame(): array
    {
        return $this->board_game;
    }

    /**
     * @param array $board_game
     */
    public function setBoardGame(array $board_game): void
    {
        $this->board_game = $board_game;
    }

    /**
     * @return int
     */
    public function getTypeGame(): int
    {
        return $this->type_game;
    }

    /**
     * @param int $type_game
     */
    public function setTypeGame(int $type_game): void
    {
        $this->type_game = $type_game;
    }


    /**
     * @return string
     */
    public function getTurn(): string
    {
        return $this->turn;
    }

    /**
     * @param string $turn
     */
    public function setTurn(string $turn): void
    {
        $this->turn = $turn;
    }

    /**
     * @return bool
     */
    public function isState(): bool
    {
        return $this->state;
    }

    /**
     * @param bool $state
     */
    public function setState(bool $state): void
    {
        $this->state = $state;
    }



}
