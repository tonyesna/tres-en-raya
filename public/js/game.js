/**
 * Function that checks if we can continue clicking on the board.
 * @param position
 * @returns {boolean}
 */

function iCanClick(position) {
    return $(".position_" + position).text() === "";
}

/**
 * Function that checks if the game is over and displays the Winner and the New Game button.
 * @param data
 */
function isEndGame(data) {
    if (data.state) {
        if (data.winner.length !== 0) {
            $('.position_' + data.winner.combination[0]).css("color", "#00e676");
            $('.position_' + data.winner.combination[1]).css("color", "#00e676");
            $('.position_' + data.winner.combination[2]).css("color", "#00e676");
            $('.sqare').prop('disabled', true);
            $('.player-turn').text("GANADOR " + data.winner.player).css("color", "#00e676");
        } else {
            $('.player-turn').text("EMPATE ")
        }
        $('.new-game').css("display", "block");
    }
}


/**
 * Function that sends the data of each move
 * @param position
 * @param type_game
 */
function sendDataGame(position,type_game) {

    if (iCanClick(position)) {
        let turn = $('#myturn').text();
        turn = turn.toLowerCase();

        if (turn === "player 1") {
            $(".position_" + position).text("X")
        } else {
            $(".position_" + position).text("O")
        }

        $.ajax({
            type: 'POST',
            url: "game",
            data: {"_token": $("meta[name='csrf-token']").attr("content"),
                "position": position, "turn": turn, "type_game": type_game},
            dataType: "json",
            success: function (data) {
                turn = data.data.turn;
                type_game = data.data.type_game;

                let move_computer = data.data.move_computer;
                if(type_game === "2"){

                    $(".position_" + move_computer).text("O")
                }

                isEndGame(data.data);
                $('#myturn').text(turn);

            },
            error: function () {
                //Acciones si error
                console.error("Se ha producido un ERROR");
            }
        });
    }

}

/**
 * Function that creates a new game
 * @param type_game
 */
function newGame(type_game) {

    $.ajax({
        type: 'POST',
        url: "game/new",
        data: {"_token": $("meta[name='csrf-token']").attr("content"),'type_game':type_game},
        dataType: "json",
        success: function (data) {
            location.reload();

        },
        error: function () {
            //Acciones si error
            location.reload();
        }
    });

}
