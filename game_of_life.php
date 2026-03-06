<?php
// game_of_life.php
// run in CLI: php game_of_life.php

if (PHP_SAPI !== 'cli') {
    echo "This script is intended to be run from the command line.\n";
    exit(1);
}

$rows = 25;
$cols = 25;

// Assumption generation 
$gens = 20;

// initialize empty grid
$grid = [];
for ($r = 0; $r < $rows; $r++) {
    $grid[$r] = array_fill(0, $cols, 0);
}

// Glider pattern
$glider = [
    [0, 1],
    [1, 2],
    [2, 0],
    [2, 1],
    [2, 2],
];

// Put glider centered in the 25x25 grid
$startR = intdiv($rows, 2) - 1;
$startC = intdiv($cols, 2) - 1;
foreach ($glider as [$d_r, $d_c]) {
    $row = $startR + $d_r;
    $col = $startC + $d_c;
    if ($row >= 0 && $row < $rows && $col >= 0 && $col < $cols) {
        $grid[$row][$col] = 1;
    }
}

function countNeighbors(array $grid, int $r, int $c, int $rows, int $cols): int {
    $count = 0;
    for ($dr = -1; $dr <= 1; $dr++) {
        for ($dc = -1; $dc <= 1; $dc++) {
            if ($dr === 0 && $dc === 0) {
                continue;
            }
            $nr = $r + $dr;
            $nc = $c + $dc;
            if ($nr >= 0 && $nr < $rows && $nc >= 0 && $nc < $cols) {
                if ($grid[$nr][$nc] === 1) {
                    $count++;
                }
            }
        }
    }
    return $count;
}

function nextGeneration(array $grid, int $rows, int $cols): array {
    $new = [];
    for ($r = 0; $r < $rows; $r++) {
        $new[$r] = array_fill(0, $cols, 0);
    }

    for ($r = 0; $r < $rows; $r++) {
        for ($c = 0; $c < $cols; $c++) {
            $liveNeighbors = countNeighbors($grid, $r, $c, $rows, $cols);
            if ($grid[$r][$c] === 1) {
                // live cell
                if ($liveNeighbors < 2) {
                    $new[$r][$c] = 0; // underpopulation
                } elseif ($liveNeighbors === 2 || $liveNeighbors === 3) {
                    $new[$r][$c] = 1; // stays alive
                } else {
                    $new[$r][$c] = 0; // overcrowding
                }
            } else {
                // dead cell
                if ($liveNeighbors === 3) {
                    $new[$r][$c] = 1; // reproduction
                } else {
                    $new[$r][$c] = 0;
                }
            }
        }
    }

    return $new;
}

function render(array $grid, int $rows, int $cols, int $gen) {
    echo "Game of Life — Generation: $gen\n";
    for ($row = 0; $row < $rows; $row++) {
        for ($col = 0; $col < $cols; $col++) {
            echo $grid[$row][$col] ? ' 0 ' : ' . ';
        }
        echo "\n";
    }
}

// run generations
for ($g = 0; $g < $gens; $g++) {
    render($grid, $rows, $cols, $g);
    usleep(180000);
    $grid = nextGeneration($grid, $rows, $cols);
}

// final render
render($grid, $rows, $cols, $gens);
echo "\nGenerations complete.\n";