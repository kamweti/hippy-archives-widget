<ul class="hippy-archive-list">
 <?php foreach($archive as $year => $months) { ?>
    <li>
      <strong class="year"><?php echo $year; ?></strong>
      <ul>
        <?php foreach($months as $month => $posts) { ?>
          <li>
            <?php if( $posts > 0 ) { ?>
              <a href="<?php echo get_month_link( $year, date('m', strtotime($month) ) ) ?>">
                <?php echo $month ?>
                <?php if( $c == '1') { ?>
                  <span>(<?php echo $posts ?>)</span>
                <?php } ?>
              </a>
            <?php } else { ?>
              <span>
                <?php echo $month ?>
              </span>
            <?php } ?>
          </li>
        <?php } ?>
      </ul>
    </li>
 <?php } ?>
</ul>


