<?php

class daedalus
{
    private array $grid = [];
    private array $visited = [];
    private array $stack = [];
    private array $neighbors = [];
    private int $n_neighbors = 0;
    public function __construct(
        public int $size = 100
    ){
        for ($y = 0; $y < $this->size; $y++) {
            $this->grid[$y] = [];
            $this->visited[$y] = [];  
            for ($x = 0; $x < $this->size; $x++) {
                $this->grid[$y][$x] = [1, 1, 1, 1];
                $this->visited[$y][$x] = 0;
            }
        }
    }
    /**
     * @throws Exception
     */
    private function random_neighbor(int $x, int $y): array
    {

        return $this->neighbors[random_int(0, ($this->n_neighbors - 1))];
    }
    private function query_unvisited_neighbors(int $x, int $y): bool
    {
        $this->neighbors = [];
        if (!($x - 1 < 0) && !$this->visited[$y][$x - 1]) $this->neighbors[] = [-1, 0];
        if (!($y + 1 > ($this->size - 1)) && !$this->visited[$y + 1][$x]) $this->neighbors[] = [0, 1];
        if (!($x + 1 > ($this->size - 1)) && !$this->visited[$y][$x + 1]) $this->neighbors[] = [1, 0];
        if (!($y - 1 < 0) && !$this->visited[$y - 1][$x]) $this->neighbors[] = [0, -1];
        $this->n_neighbors = count($this->neighbors);
        return (bool)$this->n_neighbors;
    }
    private function remove_wall(int $x, int $y, int $dX, int $dY): void
    {
        // Wall order in grid cell is left, up, right, down
        switch ([$dX, $dY]) {
            case [-1, 0]:
                $this->grid[$y][$x][0] = 0;
                $this->grid[$y + $dY][$x + $dX][2] = 0;
                break;
            case [0, 1]:
                $this->grid[$y][$x][1] = 0;
                $this->grid[$y + $dY][$x + $dX][3] = 0;
                break;
            case [1, 0]:
                $this->grid[$y][$x][2] = 0;
                $this->grid[$y + $dY][$x + $dX][0] = 0;
                break;
            case [0, -1]:
                $this->grid[$y][$x][3] = 0;
                $this->grid[$y + $dY][$x + $dX][1] = 0;
                break;
        };
    }
    /**
     * @throws Exception
     */
    public function generate(): void
    {
        $x = random_int(0, $this->size);
        $y = random_int(0, $this->size);
        $this->stack[] = [$x, $y];
        while(1) {
            $this->visited[$y][$x] = 1;
            if ($this->query_unvisited_neighbors(x: $x, y: $y)) {
                [$dX, $dY] = $this->random_neighbor(x: $x, y: $y);
                $this->remove_wall(x: $x, y: $y, dX: $dX, dY: $dY);
                $x += $dX;
                $y += $dY;
                $this->stack[] = [$x, $y];
            } else {
                $i = count($this->stack) - 1;
                while (!$this->query_unvisited_neighbors(x: $x, y: $y)) {
                    $i--;
                    array_pop(array: $this->stack);
                    if ($i == 0) break 2;
                    [$x, $y] = $this->stack[$i];
                }
            }
        }
        $this->grid[0][random_int(0, $this->size)][3] = 0;
        $this->grid[$this->size - 1][random_int(0, $this->size)][1] = 0;
        unset($this->stack[0]);
    }
    public function print(): void
    {
        echo "
            <style>
                .grid {
                    width: ".($this->size * 12)."px;
                    height: ".($this->size * 12)."px;
                    display: flex;
                    flex-flow: row wrap;
                }
            </style>
        ";
        echo '<div class="grid">';
        for ($y = $this->size - 1; $y >= 0; $y--) {
            for ($x = 0; $x < $this->size; $x++) {
                echo '
                    <div class="cell">
                        <div class="'.($this->grid[$y][$x][0] ? 'solid' : 'empty').' left"></div>
                        <div class="'.($this->grid[$y][$x][1] ? 'solid' : 'empty').' up"></div>
                        <div class="'.($this->grid[$y][$x][2] ? 'solid' : 'empty').' right"></div>
                        <div class="'.($this->grid[$y][$x][3] ? 'solid' : 'empty').' down"></div>
                        <div class="solid corner corneri"></div>
                        <div class="solid corner cornerii"></div>
                        <div class="solid corner corneriii"></div>
                        <div class="solid corner corneriv"></div>
                    </div>
                ';
            }
        }
        echo '</div>';
    }
}
