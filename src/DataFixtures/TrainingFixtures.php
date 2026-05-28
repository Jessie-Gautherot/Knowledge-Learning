<?php

namespace App\DataFixtures;

use App\Entity\Theme;
use App\Entity\Cursus;
use App\Entity\Lesson;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Class TrainingFixtures
 *
 * Load training data into database.
 *
 * This fixture create:
 * - Themes
 * - Cursus
 * - Lessons
 */
class TrainingFixtures extends Fixture
{
    /**
     * Load training data into database
     *
     * @param ObjectManager $manager Doctrine entity manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        $themes = [

            'Musique' => [
                [
                    'title' => 'Cursus d’initiation à la guitare',
                    'price' => 5000,
                    'lessons' => [
                        [
                            'title' => 'Découverte de l’instrument',
                            'price' => 2600
                        ],
                        [
                            'title' => 'Les accords et les gammes',
                            'price' => 2600
                        ],
                    ]
                ],

                [
                    'title' => 'Cursus d’initiation au piano',
                    'price' => 5000,
                    'lessons' => [
                        [
                            'title' => 'Découverte de l’instrument',
                            'price' => 2600
                        ],
                        [
                            'title' => 'Les accords et les gammes',
                            'price' => 2600
                        ],
                    ]
                ]
            ],

            'Informatique' => [
                [
                    'title' => 'Cursus d’initiation au développement web',
                    'price' => 6000,
                    'lessons' => [
                        [
                            'title' => 'Les langages Html et CSS',
                            'price' => 3200
                        ],
                        [
                            'title' => 'Dynamiser votre site avec Javascript',
                            'price' => 3200
                        ],
                    ]
                ]
            ],

            'Jardinage' => [
                [
                    'title' => 'Cursus d’initiation au jardinage',
                    'price' => 3000,
                    'lessons' => [
                        [
                            'title' => 'Les outils du jardinier',
                            'price' => 1600
                        ],
                        [
                            'title' => 'Jardiner avec la lune',
                            'price' => 1600
                        ],
                    ]
                ]
            ],

            'Cuisine' => [
                [
                    'title' => 'Cursus d’initiation à la cuisine',
                    'price' => 4400,
                    'lessons' => [
                        [
                            'title' => 'Les modes de cuisson',
                            'price' => 2300
                        ],
                        [
                            'title' => 'Les saveurs',
                            'price' => 2300
                        ],
                    ]
                ],

                [
                    'title' => 'Cursus d’initiation à l’art du dressage culinaire',
                    'price' => 4800,
                    'lessons' => [
                        [
                            'title' => 'Mettre en oeuvre le style dans l’assiette',
                            'price' => 2600
                        ],
                        [
                            'title' => 'Harmoniser un repas à quatre plats',
                            'price' => 2600
                        ],
                    ]
                ]
            ]
        ];

        /**
         * Loop through all themes
         */
        foreach ($themes as $themeName => $cursusList) {

            $theme = new Theme();
            $theme->setName($themeName);

            /**
             * Loop through cursus of current theme
             */
            foreach ($cursusList as $cursusData) {

                $cursus = new Cursus();

                $cursus->setTitle($cursusData['title']);
                $cursus->setPrice($cursusData['price']);

                // Attach cursus to theme
                $theme->addCursus($cursus);

                /**
                 * Loop through lessons of current cursus
                 */
                foreach ($cursusData['lessons'] as $lessonData) {

                    $lesson = new Lesson();

                    $lesson->setTitle($lessonData['title']);

                    $lesson->setContent(
                        'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
                    );

                    $lesson->setVideoUrl(
                        'https://www.youtube.com/embed/6f3N3-fLwAA'
                    );

                    $lesson->setPrice($lessonData['price']);

                    // Attach lesson to cursus
                    $cursus->addLesson($lesson);
                }
            }

            // Persist theme with related cursus and lessons
            $manager->persist($theme);
        }

        // Save all data
        $manager->flush();
    }
}