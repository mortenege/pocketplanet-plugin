<?php
  // load iamge id from database
  $pp_widgets_background_image_id = get_option('pp_widgets_background_image', 0);
  // get url for image_id
  $pp_widgets_image_url = wp_get_attachment_url( $pp_widgets_background_image_id );
  if ($pp_widgets_image_url) {
    $style_attr = "style=\"background: url({$pp_widgets_image_url}) no-repeat; background-size: cover;\"";
  } else {
    $style_attr = '';
  }
?>

<div id="pp-widgets-full-width-search" <?php echo $style_attr; ?>>
  <div>
  <h1 class="pp-widgets-text">Search Flights</h1>
  <div class="pp-widgets-search-area">
    <div class="form-check form-check-inline">
      <label class="pp-widgets-radio" for="exampleRadios1">
        <input type="radio" name="oneway" id="exampleRadios1" value="0" checked>
        <span></span>
        <p>Return</p>
      </label>
    </div>
    <div class="form-check form-check-inline">
      <label class="pp-widgets-radio" for="exampleRadios2">
        <input type="radio" name="oneway" id="exampleRadios2" value="1">
        <span></span>
        <p>One way</p>
      </label>
    </div>
    <div class="pp-widgets-row">
      <div class="pp-widgets-col pp-widgets-col-lg">
        <div class="form-group">
          <label for="pp-widgets-origin">Origin</label>
          <input type="text" name="origin" id="pp-widgets-origin" class="form-control" placeholder="Where are you flying from?"/>
        </div>
      </div>
      <div class="pp-widgets-col pp-widgets-col-lg">
        <div class="form-group">
          <label for="pp-widgets-destination">Destination</label>
          <input type="text" name="destination" id="pp-widgets-destination" class="form-control" placeholder="Where are you going?"/>
        </div>
      </div>
      <div class="pp-widgets-col pp-widgets-col-md">
        <div class="form-group">
          <label for="pp-widgets-date1">Departing</label>
          <input type="text" name="date1" id="pp-widgets-date1" class="form-control" placeholder="yyyy-mm-dd" />
        </div>
      </div>
      <div class="pp-widgets-col pp-widgets-col-md">
        <div class="form-group">
          <label for="pp-widgets-date2">Arriving</label>
          <input type="text" name="date2" id="pp-widgets-date2" class="form-control" placeholder="yyyy-mm-dd" />
        </div>
      </div>
      <div class="pp-widgets-col pp-widgets-col-sm">
        <div class="form-group">
          <label for="pp-widgets-travelers">Travelers</label>
          <select name="travelers" class="form-control" id="pp-widgets-travelers">
            <option>1</option>
            <option selected>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
            <option>6</option>
          </select>
        </div>
      </div>
      <div class="pp-widgets-col pp-widgets-col-sm">
        <div class="form-group">
          <label for="pp-widgets-class">Class</label>
          <select name="travelers" class="form-control" id="pp-widgets-class">
            <option selected>Economy</option>
            <option>Business</option>
            <option>First</option>
          </select>
        </div>
      </div>
      <div class="pp-widgets-col pp-widgets-col-sm">
        <div class="form-group">
          <label>&nbsp;</label>
          <input type="submit" name="submit" value="Search" class="form-control"/>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
  </div>
</div>