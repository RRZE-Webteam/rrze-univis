<?php 

namespace RRZE\UnivIS;

echo '<div class="rrze-univis">';

if ($lecture){
    $lang = get_locale();
    $type = $lecture['lecture_type_long'];

    echo '<div itemscope itemtype="https://schema.org/Course">';

    echo '<h' . $this->atts['hstart'] . '>';
    if ($lang != 'de_DE' && $lang != 'de_DE_formal' && !empty($lecture['ects_name'])) {
        $lecture['title'] = $lecture['ects_name'];
    } else {
        $lecture['title'] = $lecture['name'];
    }
    echo '<span itemprop="name">' . $lecture['title'] . '</span>';

    // echo '<span itemprop="provider" itemscope itemtype="http://schema.org/EducationalOrganization">;

    echo '</h' . $this->atts['hstart'] . '>';
    if (!empty($lecture['lecturers'])){
        echo '<h' . ($this->atts['hstart'] + 1) . '>' . __('Lecturers', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 1) . '>';
		echo '<ul>';
        foreach ($lecture['lecturers'] as $doz){
            $name = array();
            if (!empty($doz['title'])){
                $name['title'] = '<span itemprop="honorificPrefix">' . $doz['title'] . '</span>';
            }
            if (!empty($doz['firstname'])){
                $name['firstname'] = '<span itemprop="givenName">' . $doz['firstname'] . '</span>';
            }
            if (!empty($doz['lastname'])){
                $name['lastname'] = '<span itemprop="familyName">' . $doz['lastname'] . '</span>';
            }
            $fullname = implode(' ', $name);
            if (!empty($doz['person_id'])){
                $url = '<a href="' . get_permalink() . 'univisid/' . $doz['person_id'] . '">' . $fullname . '</a>';
            }else{
                $url = $fullname;
            }
			echo '<li itemprop="provider" itemscope itemtype="http://schema.org/Person">' . $url . '</li>';
        }
        echo '</ul>';
    }
    echo '<h' . ($this->atts['hstart'] + 1) . '>' . __('Details', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 1) . '>';

    if (!empty($lecture['angaben'])){
        echo '<p>' . make_clickable($lecture['angaben']) . '</p>';
    }

    echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Time and place', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
    if (array_key_exists('comment', $lecture)){
        echo '<p>' . make_clickable($lecture['comment']) . '</p>';
    }
    echo '<ul>';
    if (isset($lecture['courses'])){
        foreach ($lecture['courses'] as $course){
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
                    $aIcsLink = Functions::makeLinkToICS($type, $lecture, $term, $t);
                    $t['ics'] = '<span class="lecture-info-ics" itemprop="ics"><a href="' . $aIcsLink['link'] . '" aria-label="' . $aIcsLink['linkTxt'] . '">' . __('ICS', 'rrze-univis') . '</a></span>';
                }
                $t['time'] .= ',';
                $term_formatted = implode(' ', $t);
                echo '<li>' . $term_formatted . '</li>';
            }
        }
    }else{
        echo '<li>' . __('Time and place on appointment', 'rrze-univis') . '</li>';
    }
    echo '</ul>';

    if (array_key_exists('studs', $lecture) && array_key_exists('stud', $lecture['studs'][0])){
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Fields of study', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
        echo '<ul>';
        foreach ($lecture['studs'][0]['stud'] as $stud){
            $s = array();
            if (!empty($stud['pflicht'])){
                $s['pflicht'] = $stud['pflicht'];
            }
            if (!empty($stud['richt'])){
                $s['richt'] = $stud['richt'];
            }
            if (!empty($stud['sem'][0]) && absint($stud['sem'][0])){
                $s['sem'] = sprintf('%s %d', __('from SEM', 'rrze-univis'), absint($stud['sem'][0]));
            }
            $studinfo = implode(' ', $s);
            echo '<li>' . $studinfo . '</li>';
        }
        echo '</ul>';
    }

    if (!empty($lecture['organizational'])){
        echo '<h4>' . __('Prerequisites / Organizational information', 'rrze-univis') . '</h4>';
        echo '<p>' . $lecture['organizational'] . '</p>';
    }
    if (!empty($lecture['summary'])) {
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Content', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
        echo '<p itemprop="description">' . make_clickable($lecture['summary']) . '</p>';
    }
    if (!empty($lecture['literature'])) {
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Recommended Literature', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
        echo '<p>' . make_clickable($lecture['literature']) . '</p>';
    }
    if (!empty($lecture['ects_infos'])) {
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('ECTS information', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
        if (!empty($lecture['ects_name'])) {
            echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Title', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 3) . '>';
            echo '<p>' . $lecture['ects_name'] . '</p>';
        }
        if (!empty($lecture['ects_cred'])) {
            echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Credits', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 3) . '>';
            echo '<p>' . $lecture['ects_cred'] . '</p>';
        }
        if (!empty($lecture['ects_summary'])) {
            echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Content', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 3) . '>';
            echo '<p>' . $lecture['ects_summary'] . '</p>';
        }
        if (!empty($lecture['ects_literature'])) {
            echo '<h' . ($this->atts['hstart'] + 3) . '>' . __('Literature', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 3) . '>';
            echo '<p>' . $lecture['ects_literature'] . '</p>';
        }
    }
    if (!empty($lecture['keywords']) || !empty($lecture['maxturnout']) || !empty($lecture['url_description'])) {
        echo '<h' . ($this->atts['hstart'] + 2) . '>' . __('Additional information', 'rrze-univis') . '</h' . ($this->atts['hstart'] + 2) . '>';
        if (!empty($lecture['keywords'])) {
            echo '<p>' . __('Keywords', 'rrze-univis') . ': ' . $lecture['keywords'] . '</p>';
        }
        if (!empty($lecture['maxturnout'])) {
            echo '<p>' . __('Expected participants', 'rrze-univis') . ': ' . $lecture['maxturnout'] . '</p>';
        }
        if (!empty($lecture['url_description'])) {
            echo '<p>www: <a href="' . $lecture['url_description'] . '">' . $lecture['url_description'] . '</a></p>';
        }
    }

// echo '<div itemprop="provider" itemscope itemtype="https://schema.org/provider">';
// echo '<span itemprop="name">FAU</span>';
// echo '<span itemprop="url">https://www.fau.de</span>';
// echo '</div>';

    echo '</div>'; // schema

}
echo '</div>';