<?php
// Displays form to validate before linking accounts
  ob_start;
  ?>

  <div class="lms-login" >

      <form data-form="lms-signup">

          <label class="lms-login__field-row" data-field="username">Training account username
              <input class="lms-login__field -text" type="text" placeholder="" name="username">
          </label>

          <label class="lms-login__field-row" data-field="password">Training account password
              <input class="lms-login__field -text" type="password" placeholder="Your password" name="password">
          </label>

          <div class="lms-login__feedback" data-feedback style="display:none;"></div>
          <div class="lms-login__error" data-error style="display:none;"></div>

          <div class="lms-login__field-row lms-login__controls">
              <button class="button" type="submit">Create Account</button>
              <a href="#" class="button -inverted" >Not Now</a>
          </div>

      </form>

  </div>


  <?php
  return ob_get_clean;
