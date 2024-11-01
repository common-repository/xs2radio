<?php

class XS2Radio_Analytics
{

    public function generate_csv()
    {
        $data = $this->get_data();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="xs2radio-analytics.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $fp = fopen('php://output', 'w');
        fputcsv($fp, [
            __( 'Post ID', 'xs2radio' ),
            __( 'Post Title', 'xs2radio' ),
            __( 'Post Creation Date', 'xs2radio' ),
            __( 'Entry ID', 'xs2radio' ),
            __( 'Started', 'xs2radio' ),
            __( 'Halfway', 'xs2radio' ),
            __( 'Finished', 'xs2radio' ),
        ]);
        foreach ($data as $row) {
            fputcsv($fp, [
                intval($row->ID),
                $row->post_title,
                date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $row->post_date ) ),
                intval($row->entry_id),
                intval($row->started),
                intval($row->halfway),
                intval($row->finished),
            ]);
        }

        exit;
    }

    public function get_data()
    {
        global $wpdb;

        $query = "SELECT ID, post_title, post_date,
			MAX(CASE WHEN meta_key = 'xs2radio_entry_id' THEN meta_value ELSE NULL END) as entry_id,
			MAX(CASE WHEN meta_key = 'xs2radio_stats_started' THEN meta_value ELSE NULL END) as started,
			MAX(CASE WHEN meta_key = 'xs2radio_stats_halfway' THEN meta_value ELSE NULL END) as halfway,
			MAX(CASE WHEN meta_key = 'xs2radio_stats_finished' THEN meta_value ELSE NULL END) as finished
			FROM " . $wpdb->postmeta . " as meta
			INNER JOIN " . $wpdb->posts . " as posts ON posts.ID = meta.post_id
			WHERE posts.post_status = 'publish' AND meta_key IN ('xs2radio_entry_id', 'xs2radio_stats_started', 'xs2radio_stats_halfway', 'xs2radio_stats_finished')
			GROUP BY meta.post_id
			HAVING started IS NOT NULL;";

        $results = $wpdb->get_results( $query );

        return $results;
    }

}