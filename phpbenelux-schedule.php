<?php
/*
Plugin Name: PHPBenelux Schedule
Version: 1.0
Plugin URI: http://phpbenelux.eu
Description: Schedule plugin 2017
Author: Martin de Keijzer
Author URI: http://phpbenelux.eu/
*/

add_action( 'widgets_init', 'phpbenelux_schedule_widget_init' );

function phpbenelux_schedule_widget_init() {
    register_widget( 'phpbenelux_schedule_widget' );
}

class phpbenelux_schedule_widget extends WP_Widget
{
    const FRIDAY = 26;
    const SATURDAY = 27;

    /**
     * @var array
     */
    protected $schedule;

    /**
     * phpbenelux_schedule_widget constructor
     */
    public function __construct()
    {
        $widget_details = array(
            'classname' => 'phpbenelux_schedule_widget',
            'description' => 'Shows the schedule for PHPBenelux Conference'
        );

        parent::__construct( 'phpbenelux_schedule_widget', 'PHPBenelux schedule Widget', $widget_details );

    }

    /**
     * @param array $instance
     * @return void
     */
    public function form($instance) {
        // Backend Form
    }

    /**
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance) {
        ?>
        <p>Jump to:</p>
        <div class="row jumplist">
            <div class="col-md-4"><a class="btn btn-info" href="#tutorials">Friday Tutorials</a></div>
            <div class="col-md-4"><a class="btn btn-info" href="#friday-afternoon">Friday Conference</a></div>
            <div class="col-md-4"><a class="btn btn-info" href="#saturday">Saturday Conference</a></div>
        </div>
        <div class="row">
            <div class="col-xs-12">&nbsp;</div>
        </div>
        <div class="schedule">
            <header class="post-heading clearfix">
                <h2 class="date-inner">Friday</h2>
                <div class="post-title-wrapper">
                    <h3 class="post-title">Morning tutorials (tutorial tickets only)</h3></div>
            </header>
            <a name="tutorials">&nbsp;</a>
            <p>For the tutorial rooms you'll be given directions upon registration.</p>
            <?php
                $this->renderTutorials();
            ?>
            <div class="row">
                <div class="col-xs-2 with-border">12:30 - 13:00</div>
                <div class="col-xs-10 text-center with-border"><h5><a href="#">Tutorial lunch</a></h5></div>
            </div>
            <div class="row">
                <div class="col-xs-12">&nbsp;</div>
            </div>

            <header class="post-heading clearfix">
                <h2 class="date-inner">Friday</h2>
                <div class="post-title-wrapper">
                    <h3 class="post-title">Afternoon conference</h3></div>
            </header>
            <a name="friday-afternoon">&nbsp;</a>
            <div class="row hidden-xs">
                <div class="col-xs-2 with-border">&nbsp;</div>
                <div class="col-xs-10 text-center with-border"><h3>Auditorium</h3></div>
            </div>
            <div class="row">
                <div class="col-xs-2 with-border">13:20 - 13:30</div>
                <div class="col-xs-10 text-center with-border"><h5><a href="#">Welcome &amp; Introduction</a></h5></div>
            </div>
            <div class="row">
                <div class="col-xs-2 with-border">13:30 - 14:30</div>
                <div class="col-xs-10 text-center with-border">
                    <?php $this->renderKeynote(); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">&nbsp;</div>
            </div>

            <div class="row hidden-xs">
                <div class="col-xs-2 col-xs-2-5 with-border">&nbsp;</div>
                <div class="col-xs-2 col-xs-2-5 with-border"><h3>Track A<br> (Beethoven)</h3></div>
                <div class="col-xs-2 col-xs-2-5 with-border"><h3>Track B<br> (Permeke)</h3></div>
                <div class="col-xs-2 col-xs-2-5 with-border"><h3>Track C<br> (Rubens)</h3></div>
                <div class="col-xs-2 col-xs-2-5 with-border"><h3>Uncon<br> (Van Gogh)</h3></div>
            </div>
            <?php
                $this->renderTalks(self::FRIDAY);
            ?>
            <div class="row">
                <div class="col-xs-12">&nbsp;</div>
            </div>

            <header class="post-heading clearfix">
                <h2 class="date-inner">Saturday</h2>
                <div class="post-title-wrapper">
                    <h3 class="post-title">Conference</h3></div>
            </header>
            <a name="saturday">&nbsp;</a>
            <div class="row hidden-xs">
                <div class="col-xs-2 col-xs-2-5 with-border hidden-xs">&nbsp;</div>
                <div class="col-xs-2 col-xs-2-5 with-border"><h3>Track A<br> (Beethoven)</h3></div>
                <div class="col-xs-2 col-xs-2-5 with-border"><h3>Track B<br> (Permeke)</h3></div>
                <div class="col-xs-2 col-xs-2-5 with-border"><h3>Track C<br> (Rubens)</h3></div>
                <div class="col-xs-2 col-xs-2-5 with-border"><h3>Uncon<br> (Van Gogh)</h3></div>
            </div>
            <?php
                $this->renderTalks(self::SATURDAY);
            ?>
        </div>
        <?php
    }

    /**
     * Render the keynote session
     */
    protected function renderKeynote()
    {
        $args = array(
            'numberposts'	=> -1,
            'post_type'		=> 'sessions',
            'meta_key'	    => 'session_type',
            'meta_value'    => 'keynote',
        );
        $the_query = new WP_Query( $args );
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();
                /** @var array $speakers */
                $speakers = get_field('speakers');
                ?>
                    <h5><a href="<?php echo get_the_permalink()?>">
                        <?php echo the_title(); ?>
                    </a></h5>
                <?php
                if ($speakers) {
                    foreach ($speakers as $speaker) {
                        ?><a href="<?php echo get_the_permalink($speaker->ID); ?>"><?php echo get_the_title( $speaker->ID ); ?></a><?php
                    }
                }
            }
        } else {
            echo 'To be announced soon...';
        }
        wp_reset_query();
    }

    /**
     * Render the tutorial sessions
     */
    protected function renderTutorials()
    {
        $args = array(
            'numberposts'	=> -1,
            'post_type'		=> 'sessions',
            'meta_key'	    => 'session_type',
            'meta_value'    => 'tutorial',
        );
        $the_query = new WP_Query( $args );
        if ($the_query->have_posts()) {
            $postCounter = 0;
            while ($the_query->have_posts()) {
                if ($postCounter % 4 === 0) {
                    if ($postCounter !== 0) {
                        echo '</div>';
                    }
                    ?>
                        <div class="row">
                            <div class="col-xs-2 col-xs-2-5 with-border">
                                09:00 - 12:30
                            </div>
                    <?php
                }
                $the_query->the_post();
                /** @var array $speakers */
                $speakers = get_field('speakers');
                ?>
                    <div class="col-xs-2 col-xs-2-5 with-border">
                            <h5><a href="<?php echo get_the_permalink()?>"><?php echo the_title(); ?></a></h5>
                        <?php
                        if ($speakers) {
                            foreach ($speakers as $speaker) {
                                ?><a href="<?php echo get_the_permalink($speaker->ID); ?>"><?php
                                echo get_the_title( $speaker->ID );
                                ?></a><?php
                            }
                        }
                        ?>
                    </div>
                <?php
                $postCounter++;
            }
            ?></div><?php
        }
        wp_reset_query();
        wp_reset_postdata();
    }

    /**
     * Render the talks for a certain day
     * @param int $dayToShow
     */
    protected function renderTalks($dayToShow)
    {
        if ($this->schedule === null) {
            $this->loadSchedule();
        }

        /** @var array $todaysSchedule */
        $todaysSchedule = $this->schedule[$dayToShow];
        ksort($todaysSchedule);

        /** @var array $timeSlot */
        foreach ($todaysSchedule as $timeSlot) {
            ksort($timeSlot);
            $firstItem = reset($timeSlot);
            if ((int) $firstItem['start'] <= 1516973400) {
                continue;
            }
            $startTime = new \DateTime('@'.$firstItem['start']);
            $endTime = new \DateTime('@'.$firstItem['end']);

            $cssSessionClass = '';
            $cssTimeClass = '';
            if (count($timeSlot) > 1) {
                $cssSessionClass = 'col-xs-2 col-xs-2-5';
                $cssTimeClass = $cssSessionClass;
            }
            if (count($timeSlot) === 1) {
                $cssSessionClass = 'col-xs-10 text-center';
                $cssTimeClass = 'col-xs-2';
            }
            ?>
            <div class="row">
                <div class="<?php echo $cssTimeClass; ?> with-border"><?php echo $startTime->format('H:i') ?> - <?php echo $endTime->format('H:i') ?></div>
                <?php foreach ($timeSlot as $session) { ?>
                    <div class="<?php echo $cssSessionClass; ?> with-border">
                        <h5><a href="<?php echo $session['link']; ?>"><?php echo $session['title']; ?></a></h5>
                        <?php
                        /** @var array $speakers */
                        $speakers = $session['speakers'];
                        foreach ($speakers as $speaker) { ?>
                            <a href="<?php echo $speaker['link']; ?>"><?php echo $speaker['name']; ?></a>&nbsp;
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <?php
        }
    }

    protected function loadSchedule()
    {
        $speakers = array();
        $schedule = array(
            self::FRIDAY => array(),
            self::SATURDAY => array(),
        );
        $args = array(
            'posts_per_page' => 5000,
            'post_type'		 => 'sessions',
            'meta_query'     => array(
                'key' => 'session_type',
                'value'   => array( 'talk', 'social' ),
                'compare' => 'IN',
            )
        );
        $the_query = new WP_Query( $args );
        if ($the_query->have_posts()) {
            while ($the_query->have_posts()) {
                $the_query->the_post();

                $start = get_field('start');
                $room = get_field('room');
                $startDate = new \DateTime('@'.$start);
                $day = $startDate->format('d');

                /** @var array $registeredSpeakers */
                $registeredSpeakers = get_field('speakers');
                if ($registeredSpeakers) {
                    foreach ($registeredSpeakers as $speaker) {
                        $speakers[] = array(
                            'name' => get_the_title($speaker->ID),
                            'link' => get_the_permalink($speaker->ID),
                        );
                    }
                }

                $schedule[(int) $day][$start][$room] = array(
                    'post_id'   => get_the_ID(),
                    'start'     => get_field('start'),
                    'end'       => get_field('end'),
                    'title'     => get_the_title(),
                    'link'      => get_the_permalink(),
                    'speakers'  => $speakers,
                );
                $speakers = array();
            }
        }

        $this->schedule = $schedule;
        wp_reset_query();
        wp_reset_postdata();
    }
}