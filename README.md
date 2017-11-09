Yet another Tic-Tac-Toe Game
=========

The original task

```text
Tic-tac-toe is a game for two players, X and O, who take turns marking the spaces in a 3×3 grid.
The player who succeeds in placing three of their marks in a horizontal, vertical, or diagonal row
wins the game.

Specifications:
- Save game state in a database - use DynamoDB from AWS
https://aws.amazon.com/free/
- Do not implement client​, sending requests over HTTP, console or using forms is
enough
- Client should know that he won or tied after he placed the last X or O
- There should be at least two API commands - JoinBattle and PlaceMarker
- Enforce players to take turns, do not let the same player place 2 markers in a row
- Implement simple matchmaking - players will join a match, if there is one created or will
create new one, if there isn’t anyone waiting for another player
- There should be an account management as well
```

## Installation

Simple installation

```bash
$ git clone https://github.com/pavlobalandin/tictactoe.git
$ cd tictactoe
$ composer install

$ chmod 777 var/logs
$ chmod 777 var/cache
$ chmod 777 var/sessions
$ chmod 777 db
```

## Database options

No database required.

## Launch application

```bash
php bin/console server:run 0.0.0.0:8000
```

Now open http://localhost:8000/

In a new tab of the same browser open the same url.
On both tabs press button ENTER GAME

Field is grey when other player is maing a move.

To start a new game open an URL.
Game is started when TWO new players have entered the game at the same time.