<?php

class daedalus
{
    private array $grid = [];
    private array $visited = [];
    private array $stack = [];
    public function __construct(
        public int $size = 100
    ){
        for ($y = 0; $y < $this->size; $y++) {
            $this->grid[$y] = [];
            $this->visited[$y] = [];
            for ($x = 0; $x < $this->size; $x++) {
                // Wall order in array is left, up, right, down
                $this->grid[$y][$x] = [1, 1, 1, 1];
                $this->visited[$y][$x] = 0;
            }
        }
    }
    /**
     * @throws Exception
     */
    private function random_offset(int $x, int $y): array
    {
        $choices = [];
        if (!($x - 1 < 0) && !$this->visited[$y][$x - 1]) $choices[] = [-1, 0];
        if (!($y + 1 > ($this->size - 1)) && !$this->visited[$y + 1][$x]) $choices[] = [0, 1];
        if (!($x + 1 > ($this->size - 1)) && !$this->visited[$y][$x + 1]) $choices[] = [1, 0];
        if (!($y - 1 < 0) && !$this->visited[$y - 1][$x]) $choices[] = [0, -1];
        return $choices[random_int(0, (count($choices) - 1))];
    }
    private function unvisited_neighbors(int $x, int $y): bool
    {
        if (!(($x - 1) < 0) && !$this->visited[$y][$x - 1]) return true;
        if (!(($y + 1) > ($this->size - 1)) && !$this->visited[$y + 1][$x]) return true;
        if (!(($x + 1) > ($this->size - 1)) && !$this->visited[$y][$x + 1]) return true;
        if (!(($y - 1) < 0) && !$this->visited[$y - 1][$x]) return true;
        return false;
    }
    private function remove_wall(int $x, int $y, array $offset): void
    {
        switch ($offset) {
            case [-1, 0]:
                $this->grid[$y][$x][0] = 0;
                $this->grid[$y + $offset[1]][$x + $offset[0]][2] = 0;
                break;
            case [0, 1]:
                $this->grid[$y][$x][1] = 0;
                $this->grid[$y + $offset[1]][$x + $offset[0]][3] = 0;
                break;
            case [1, 0]:
                $this->grid[$y][$x][2] = 0;
                $this->grid[$y + $offset[1]][$x + $offset[0]][0] = 0;
                break;
            case [0, -1]:
                $this->grid[$y][$x][3] = 0;
                $this->grid[$y + $offset[1]][$x + $offset[0]][1] = 0;
                break;
        };
    }
    /**
     * @throws Exception
     */
    public function generate(): void
    {
        $cell = [random_int(0, $this->size), random_int(0, $this->size)];
        $this->stack[] = $cell;
        while(1) {
            $this->visited[$cell[1]][$cell[0]] = 1;
            if ($this->unvisited_neighbors(...$cell)) {
                $offset = $this->random_offset(...$cell);
                $this->remove_wall($cell[0], $cell[1], $offset);
                $cell = [$cell[0] + $offset[0], $cell[1] + $offset[1]];
                $this->stack[] = $cell;
            } else {
                $i = count($this->stack) - 1;
                while (!$this->unvisited_neighbors(...$cell)) {
                    $i--;
                    if ($i == 0) break 2;
                    array_pop($this->stack);
                    $cell = $this->stack[$i];
                }
            }
        }
        $this->grid[0][random_int(0, $this->size)][3] = 0;
        $this->grid[$this->size - 1][random_int(0, $this->size)][1] = 0;
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
                echo '<div class="cell">
                    <div class="'.($this->grid[$y][$x][0] ? 'solid ' : 'empty ').'left"></div>
                    <div class="'.($this->grid[$y][$x][1] ? 'solid ' : 'empty ').'up"></div>
                    <div class="'.($this->grid[$y][$x][2] ? 'solid ' : 'empty ').'right"></div>
                    <div class="'.($this->grid[$y][$x][3] ? 'solid ' : 'empty ').'down"></div>
                    <div class="solid corner corneri"></div>
                    <div class="solid corner cornerii"></div>
                    <div class="solid corner corneriii"></div>
                    <div class="solid corner corneriv"></div>
                </div>';
            }
        }
        echo '</div>';
    }
}
