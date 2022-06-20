<?php 

$aAllowedColors = [
    'med',
    'nat',
    'rw',
    'phil',
    'tk',
];

$aColor = array_intersect($this->show, $aAllowedColors);
$this->atts['color'] = (!empty($aColor) ? $aColor[1] : '');

$ret = '<div class="rrze-univis">';
if ($data){
    $lang = get_locale();
    $options = get_option('rrze-univis');
    $ssstart = (!empty($options['basic_ssStart']) ? $options['basic_ssStart'] : 0);
    $ssend = (!empty($options['basic_ssEnd']) ? $options['basic_ssEnd'] : 0);
    $wsstart = (!empty($options['basic_wsStart']) ? $options['basic_wsStart'] : 0);
    $wsend = (!empty($options['basic_wsEnd']) ? $options['basic_wsEnd'] : 0);

    if (in_array('accordion', $this->show)){
        $ret .= '[collapsibles hstart="' . $this->atts['hstart'] . '"]';
    }

    foreach ($data as $typ => $veranstaltungen){

        if (in_array('accordion', $this->show)){
            $ret .= '[collapse title="' . $typ . '" name="' . urlencode($typ) . '" color="' . $this->atts['color'] . '"]';
        }else{
            $ret .= '<h' . $this->atts['hstart'] . '>' . $typ . '</h' . $this->atts['hstart'] . '>';
        }

        $ret .= '<ul>';
        foreach ($veranstaltungen as $veranstaltung){
            $url = get_permalink() . 'lv_id/' . $veranstaltung['lecture_id'];
			$ret .= '<li>';
            $ret .= '<h' . ($this->atts['hstart'] + 1) . '><a href="' . $url . '">';
            if ($lang != 'de_DE' && $lang != 'de_DE_formal' && !empty($veranstaltung['ects_name'])) {
                $veranstaltung['title'] = $veranstaltung['ects_name'];
            } else {
                $veranstaltung['title'] = $veranstaltung['name'];
            }
            $ret .= $veranstaltung['title'];
            $ret .= '</a></h' . ($this->atts['hstart'] + 1) . '>';
            if (!empty($veranstaltung['comment']) && !in_array('comment', $this->hide)) {
                $ret .= '<p>' . make_clickable($veranstaltung['comment']) . '</p>';
            }
            if (!empty($veranstaltung['organizational']) && !in_array('organizational', $this->hide)) {
                $ret .= '<p>' . make_clickable($veranstaltung['organizational']) . '</p>';
            }

            $ret .= '<ul class="terminmeta">';
            $ret .= '<li>';
            $infos = '';
            if (!empty($veranstaltung['sws'])) {
                $infos .= '<span>' . $veranstaltung['sws'] . '</span>';
            }
            if (!empty($veranstaltung['maxturnout'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . __('Expected participants', 'rrze-univis') . ': ' . $veranstaltung['maxturnout'] . '</span>';
            }
            if (!empty($veranstaltung['fruehstud'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . $veranstaltung['fruehstud'] . '</span>';
            }
            if (!empty($veranstaltung['gast'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . $veranstaltung['gast'] . '</span>';
            }
            if (!empty($veranstaltung['schein'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . $veranstaltung['schein'] . '</span>';
            }
            if (!empty($veranstaltung['ects'])) {
                if (!empty($infos)) {$infos .= '; ';}
                $infos .= '<span>' . $veranstaltung['ects'] . '</span>';
                if (!empty($veranstaltung['ects_cred'])) {
                    $infos .= ' (' . $veranstaltung['ects_cred'] . ')';
                }
                $infos .= '</span>';
            }
            if (!empty($veranstaltung['leclanguage_long']) && ($veranstaltung['leclanguage_long'] != __('Lecture\'s language German', 'rrze-univis'))) {
                if (!empty($infos)) {$infos .= ', ';}
                $infos .= '<span>' . $veranstaltung['leclanguage_long'] . '</span>';
            }
            $ret .= $infos . '</li>';

			$courseDates = '<li class="termindaten">' . __('Date', 'rrze-univis') . ':';
			$courseDates .= '<ul>';

            if (isset($veranstaltung['courses'])){
                foreach ($veranstaltung['courses'] as $course){
                    if ((empty($veranstaltung['lecturer_key']) || empty($course['doz'])) || (!empty($veranstaltung['lecturer_key']) && !empty($course['doz']) && (in_array($veranstaltung['lecturer_key'], $course['doz'])))) {
                        foreach ($course['term'] as $term){
                            $t = array();
                            $time = array();
                            if (!empty($term['repeat'])){
                                $t['repeat'] = $term['repeat'];
                            }
                            if (!empty($term['startdate'])){
                                if (!empty($term['enddate']) && $term['startdate'] != $term['enddate']){
                                    $t['date'] = date("d.m.Y", strtotime($term['startdate'])) . '-' . date("d.m.Y", strtotime($term['enddate']));
                                }else{
                                    $t['date'] = date("d.m.Y", strtotime($term['startdate']));
                                }
                            }
                            if (!empty($term['starttime'])){
                                $time['starttime'] = $term['starttime'];
                            }
                            if (!empty($term['endtime'])){
                                $time['endtime'] = $term['endtime'];
                            }
                            if (!empty($time)){
                                $t['time'] = $time['starttime'] . '-' . $time['endtime'];
                            }else{
                                $t['time'] = __('Time on appointment', 'rrze-univis');
                            }
                            if (!empty($term['room']['short'])){
                                $t['room'] = __('Room', 'rrze-univis') . ' ' . $term['room']['short'];
                            }
                            if (!empty($term['exclude'])){
                                $t['exclude'] = '(' . __('exclude', 'rrze-univis') . ' ' . $term['exclude'] . ')';
                            }
                            if (!empty($course['coursename'])){
                                $t['coursename'] = '(' . __('Course', 'rrze-univis') . ' ' . $course['coursename'] . ')';
                            }
                            // ICS
                            if (in_array('ics', $this->show) && !in_array('ics', $this->hide)) {
                                $props = [
                                    'summary' => $veranstaltung['title'],
                                    'startdate' => (!empty($term['startdate']) ? $term['startdate'] : null),
                                    'enddate' => (!empty($term['enddate']) ? $term['enddate'] : null),
                                    'starttime' => (!empty($term['starttime']) ? $term['starttime'] : null),
                                    'endtime' => (!empty($term['endtime']) ? $term['endtime'] : null),
                                    'repeat' => (!empty($term['repeat']) ? $term['repeat'] : null),
                                    'location' => (!empty($t['room']) ? $t['room'] : null),
                                    'description' => (!empty($veranstaltung['comment']) ? $veranstaltung['comment'] : null),
                                    'url' => get_permalink(),
                                    'map' => (!empty($term['room']['north']) && !empty($term['room']['east']) ? 'https://karte.fau.de/api/v1/iframe/marker/' . $term['room']['north'] . ',' . $term['room']['east'] . '/zoom/16' : ''),
                                    'filename' => sanitize_file_name($typ),
                                    'ssstart' => $ssstart,
                                    'ssend' => $ssend,
                                    'wsstart' => $wsstart,
                                    'wsend' => $wsend,
                                ];

                                $screenReaderTxt = __('ICS', 'rrze-univis') . ': ' . __('Date', 'rrze-univis') . ' ' . (!empty($t['repeat']) ? $t['repeat'] : '') . ' ' . (!empty($t['date']) ? $t['date'] . ' ' : '') . $t['time'] . ' ' . __('import to calendar', 'rrze-univis');
                                $t['ics'] = '<span class="lecture-info-ics" itemprop="ics"><a href="' . plugin_dir_url(__DIR__) . 'ics.php?' . http_build_query($props) . '" aria-label="' . $screenReaderTxt . '">' . __('ICS', 'rrze-univis') . '</a></span>';
                            }
                            $t['time'] .= ',';
                            $term_formatted = implode(' ', $t);
						    $courseDates .= '<li>' . $term_formatted . '</li>';
                        }
                    }
                }
            }else{
			    $courseDates .= '<li>' . __('Time and place on appointment', 'rrze-univis') . '</li>';
            }
		    $courseDates .= '</ul>';
		    $courseDates .= '</li>';
		    $ret .= $courseDates. '</li>';
        }
        $ret .= '</ul>';

        if (in_array('accordion', $this->show)){
            $ret .= '[/collapse]';
        }
    }

    if (in_array('accordion', $this->show)){
        $ret .= '[/collapsibles]';
        $ret = do_shortcode($ret);
    }
}

$ret .= '</div>';

echo $ret;