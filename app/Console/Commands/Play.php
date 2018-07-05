<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Card;

class Play extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'play';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Play Higher or Lower';

    /**
     * User score
     *
     * @var int
     */
    protected $score = 0;
    
    /**
     * Cards
     *
     * @var int
     */
    protected $cards;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function splashScreen()
    {
        $this->line('');
        $this->line('');
        $this->line('');

        $splashscreen = file_get_contents(storage_path('splashscreen.txt'));
        foreach(preg_split("/((\r?\n)|(\r\n?))/", $splashscreen) as $line){
            $this->line($line);
        }

        $this->line('');
        $this->line('');
        $this->line('');
        $this->line('');
    }

    public function displayCard(Card $card) {
        $lines = [
            ['┌─────────┐'],
            ['│v        │'],
            ['│         │'],
            ['│         │'],
            ['│    s    │'],
            ['│         │'],
            ['│         │'],
            ['│         │'],
            ['└─────────┘']
        ];

        foreach ($lines as $line) {
            $line = str_replace('s', $card->getSymbol(), $line);
            $line = str_replace('v', $card->value, $line);
            $this->line($line);
        }
    }

    public function gameOver()
    {
        $this->error('GAME OVER!!');
    }

    public function newRound()
    {
        if($this->score !== 0){
            $this->line(sprintf('Your current score is %d', $this->score));
            $this->line('');
            $this->line('');

            $this->line('#################################');
            $this->line(sprintf('Round %d:', $this->score+1));
            $this->line('');
        }

        $card = $this->cards->random(1)->first();
        $this->line('Your card:');
        $this->displayCard($card);

        $nextCard = $this->cards->random(1)->first();

        // @todo bad user input? 
        $guess = $this->anticipate('Higher or Lower?', ['higher', 'lower']);
        
        
        if ($guess === 'higher' && $card->isHigherThan($nextCard)) {
            $this->score++;
            $this->line('');
            $this->line('');
            $this->line('Correct!');
            $this->displayCard($nextCard);
            $this->newRound();
        } elseif ($guess === 'lower' && $card->isLowerThan($nextCard)) {
            $this->score++;
            $this->line('');
            $this->line('');
            $this->line('Correct!');
            $this->displayCard($nextCard);
            $this->newRound();
        } else {
            $this->line('');
            $this->line('');
            $this->gameOver();
            $this->displayCard($nextCard);
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->splashScreen();
        $this->cards = Card::getCards();
        $this->newRound();
    }    
}
