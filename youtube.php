<?php 

session_start();

if(!empty($_SESSION['youtube_api_data'])){
  $youtube_data= $_SESSION['youtube_api_data'];
}else{
  $key= 'AIzaSyCdVvVQPGW5oR4n10tmf6gWSQqzEvz20es';
  $playlist_ID='PLYZb8VYygIYh1c_ZIL-F9oIYYvaTw9fqO';
  $url= 'https://youtube.googleapis.com/youtube/v3/playlistItems?part=snippet%2CcontentDetails&maxResults=25&prettyPrint=true&playlistId='.$playlist_ID.'&key='.$key;
  $curl = curl_init();
  
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HEADER, false);
  
  $data = curl_exec($curl);
  
  curl_close($curl);
  
  $youtube_data=$data;
  $_SESSION['youtube_api_data']=$data;
}

$youtube=json_decode($youtube_data);
$youtube_list=array();
$i=0;
foreach($youtube->items as $result){
  $youtube_list[$i]['title']=$result->snippet->title;
  $youtube_list[$i]['published_date']=$result->contentDetails->videoPublishedAt;
  $youtube_list[$i]['description']=$result->snippet->description;
  $youtube_list[$i]['contentDetails']=$result->contentDetails->videoId;
  $youtube_list[$i]['thumbnail']=$result->snippet->thumbnails->standard;
  $i++;
}
//echo "<pre>";
//print_r($youtube_list);
//exit();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap 5 Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container-fluid p-5 bg-primary text-white text-center">
  <h1>Youtube APi List</h1>
  
</div>
  
<div class="container mt-5">
  <div class="row">
    <div class="col-md-7">
    <iframe width="640" height="352" src="https://www.youtube.com/embed/<?=$youtube_list[0]['contentDetails']?>" frameborder="0" allowfullscreen=""></iframe> 
    </div>
    <div class="col-md-5">
      <?php foreach($youtube_list as $result){ ?>
      <div class="col-md-12">
        <div class="row">
        <div class="col-md-4">
          <img class="img-fluid" src="<?=$result['thumbnail']->url?>">
        </div>
        <div class="col-md-8">
          <h3><?=$result['title']?></h3>
          <p><?=$result['published_date']?></p>
        </div>
        </div>
        
      </div>
      <?php } ?>
    </div>
  
    
  </div>
</div>

</body>
</html>
