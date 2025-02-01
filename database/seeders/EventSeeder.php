<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::insert([
            [
                'name'                   => 'Obon',
                'description'            => 'The festival is based on a legend about a Buddhist monk called Mogallana. The story goes that Mogallana could see into the afterlife and saved his deceased mother from going to hell by giving offerings to Buddhist monks. Having gained redemption for his mother, he danced in celebration, joined by others in a large circle. This dance is known as the Bon Odori dance.',
                'date'                   => '2027-08-13T13:00:00',
                'total_availability'     => 10,
                'remaining_availability' => 10
            ],
            [
                'name'                   => 'Carnival',
                'description'            => 'This festival is known for being a time of great indulgence before Lent (which is a time stressing the opposite), with drinking, overeating, and various other activities of indulgence being performed. During Lent, dairy and animal products are eaten less, if at all, and individuals make a Lenten Sacrifice, thus giving up a certain object or activity of desire. On the final day of the season, Shrove Tuesday, many traditional Christians, such as Lutherans, Anglicans, and Roman Catholics, "make a special point of self-examination, of considering what wrongs they need to repent, and what amendments of life or areas of spiritual growth they especially need to ask God\'s help in dealing with.',
                'date'                   => '2013-03-03T10:00:00',
                'total_availability'     => 5,
                'remaining_availability' => 5
            ],
            [
                'name'                   => 'Swiss Yodeling Festival.',
                'description'            => 'Natural yodeling exists all over the world, but especially in mountainous andinaccessible regions where the technique was used to communicate over extendeddistances. Although yodeling was probably used back in the Stone Age, the choir singingonly developed in the 19th century.',
                'date'                   => '2025-06-17T14:00:00',
                'total_availability'     => 1,
                'remaining_availability' => 1
            ],
            [
                'name'                   => 'Tanabata Matsuri',
                'description'            => 'This event celebrates the meeting of the deities Orihime and Hikoboshi(represented by the stars Vega and Altair respectively). According to legend, the MilkyWay separates these lovers, and they are allowed to meet only once a year on the seventhday of the seventh lunar month of the lunisolar calendar.',
                'date'                   => '2007-07-07T13:00:00',
                'total_availability'     => 200,
                'remaining_availability' => 200
            ],
            [
                'name'                   => 'Sechseläuten',
                'description'            => "This Zurich Spring custom got its unusual name from the medieval custom ofringing a bell of the Grossmünster every evening at six o'clock to proclaim the end of theworking day during the summer semester. Since it marked the beginning of springtime, thefirst ringing of the bell provided a good opportunity for a celebration.",
                'date'                   => '2047-04-21T09:00:00',
                'total_availability'     => 0,
                'remaining_availability' => 0
            ]
        ]);

    }
}
