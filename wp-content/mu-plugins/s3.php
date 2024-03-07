<?php
add_filter('s3_uploads_s3_client_params', function($params) {
  $params['endpoint'] = S3_UPLOADS_URL;
  $params['use_path_style_endpoint'] = true;
  return $params;
});
