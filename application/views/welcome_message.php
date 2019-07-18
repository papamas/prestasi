<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="<?php echo base_url()?>assets/img/favicon.ico" rel="shortcut icon" type="image/x-icon">
  <title>e-Prestasi: Penilaian Prestasi Pegawai Negeri Sipil</title>

  <!-- Font Awesome Icons -->
  <link href="<?php echo base_url()?>assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Merriweather+Sans:400,700" rel="stylesheet">
  <link href='https://fonts.googleapis.com/css?family=Merriweather:400,300,300italic,400italic,700,700italic' rel='stylesheet' type='text/css'>

  <!-- Plugin CSS -->
  <link href="<?php echo base_url()?>assets/vendor/magnific-popup/magnific-popup.css" rel="stylesheet">
  
   <link href="<?php echo base_url()?>assets/vendor/select2/css/select2.min.css" rel="stylesheet" type="text/css">

  <!-- Theme CSS - Includes Bootstrap -->
  <link href="<?php echo base_url()?>assets/css/creative.min.css" rel="stylesheet">

</head>

<body id="page-top">

  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light fixed-top py-3" id="mainNav">
    <div class="container">
      <a class="navbar-brand js-scroll-trigger" href="#page-top"></a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto my-2 my-lg-0">
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#about">About</a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#services">Fasilitas</a>
          </li>
		  <?php if($logged_in):?>
          <li class="nav-item">
            <a class="nav-link" href="<?php echo site_url()?>/welcome/Logout" >Logout</a>
          </li>
		  <li class="nav-item">
            <a class="nav-link" href="#eprestasi">Print</a>
          </li>
		  <?php endif;?>
		  <?php if(!$logged_in):?>
          <li class="nav-item">
            <a class="nav-link" href="#loginModal" data-toggle="modal">Login</a>
          </li>
		  <?php endif;?>
          <li class="nav-item">
            <a class="nav-link js-scroll-trigger" href="#contact">Kontak</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Masthead -->
  <header class="masthead">
    <div class="container h-100">
      <div class="row h-100 align-items-center justify-content-center text-center">
        <div class="col-lg-10 align-self-end">
          <h1 class="text-white font-weight-bold"><span class="text-uppercase"> Aplikasi</span> e-<span class="text-uppercase">Prestasi Kerja</span></h1>
          <hr class="divider my-4">
        </div>
        <div class="col-lg-8 align-self-baseline">
          <p class="text-white-75 font-weight-light mb-5">Aplikasi e-Prestasi Kerja dapat membantu dalam melakukan Penilaian Prestasi Kerja PNS dengan menggunakan unsur SKP dan unsur Perilaku !</p>
          <a class="btn btn-primary btn-xl js-scroll-trigger" href="#services">Find Out More</a>
        </div>
      </div>
    </div>
  </header>

  <!-- About Section -->
  <section class="page-section bg-primary" id="about">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h2 class="text-white mt-0">Kami punya apa yang Anda butuhkan !</h2>
          <hr class="divider light my-4">
          <p class="text-white-50 mb-4">Aplikasi e-Prestasi Kerja memiliki apa yang anda butuhkan dalam melakukan penilaian prestasi kerja pada Aparatur Sipil Negara pada Instansi anda</p>
          <a class="btn btn-light btn-xl js-scroll-trigger" href="#services">Get Started!</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Services Section -->
  <section class="page-section" id="services">
    <div class="container">
      <h2 class="text-center mt-0">Fasilitas e-Prestasi Kerja</h2>
      <hr class="divider my-4">
      <div class="row">
	  
	    <div class="col-lg-4 col-md-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-chart-line text-primary mb-4"></i>
            <h3 class="h4 mb-2">Mengukur Capaian</h3>
            <p class="text-muted mb-0">Aplikasi e-Prestasi Kerja dapat mengukur tingkat capaian melalui 4(empat) aspek yaitu kuantitas, kualitas, waktu dan/atau biaya antara target dan realisasi kinerja yang  telah ditetapkan</p>
          </div>
        </div>
		
	    <div class="col-lg-4 col-md-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-certificate text-primary mb-4"></i>
            <h3 class="h4 mb-2">Sesuai Peraturan</h3>
            <p class="text-muted mb-0">Aplikasi e-Prestasi Kerja dapat melakukan generate formulir Penilaian Prestasi Kerja sesuai dengan Peraturan Kepala Badan Kepegawaian Negara Nomor 1 Tahun 2013.</p>
          </div>
        </div>
		
        
        
       
		<div class="col-lg-4 col-md-6 text-center">
          <div class="mt-5">
            <i class="fas fa-4x fa-user text-primary mb-4"></i>
            <h3 class="h4 mb-2">Dukungan Mutasi</h3>
            <p class="text-muted mb-0">Aplikasi e-Prestasi Kerja dapat melakukan Penyusunan dan Penilaian Prestasi Kerja Pegawai bagi PNS yang mutasi/ pindah</p>
          </div>
        </div>
        
      </div>
    </div>
  </section>

  <?php if($logged_in):?>
  <!-- eprestasi Section -->
  <section  class="page-section bg-dark text-white" id="eprestasi">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-12 col-sm-12">          
			<form method="post" action="<?php echo site_url()?>/cetak">
			  <div class="form-group">
				<label for="instansi">Instansi : </label>
				<select class="custom-select" name="instansi" required>
				  <option selected value="">--Silahkan Pilih--</option>
				  <?php foreach ($instansi->result() as $value):?>
				  <option value="<?php echo $value->id_instansi?>"><?php echo $value->nama_instansi?></option>
				  <?php endforeach; ?>
				</select>
			  </div>
			  <div class="form-group">
				<label for="tahun">Tahun Penilaian SKP : </label>
				<select class="custom-select" name="tahun" required>
				  <option selected value="">--Silahkan Pilih--</option>
				  <?php foreach ($tahun as $key=>$value):?>
				  <option value="<?php echo $value?>"><?php echo $value?></option>
				   <?php endforeach; ?>				 
				</select>
			  </div>
			  
			  <button type="submit" class="btn btn-primary">  
				<span class="fa fa-print" aria-hidden="true"></span> 
				Print Penilaian Prestasi Kerja
			  </button>
			</form>
        </div>
       
      </div>
    </div>
  </section>
  <?php endif;?>

  <!-- Contact Section -->
  <section class="page-section bg-primary text-white" id="contact">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
          <h2 class="mt-0">Let's Get In Touch!</h2>
          <hr class="divider my-4">
          <p class="mb-5">Anda telah siap berkerjasama dengan kami ? Hubungi kami atau kirim surat dan kami akan menghubungi Anda sesegera mungkin!</p>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-6 ml-auto text-center mb-5 mb-lg-0">
          <i class="fas fa-home fa-5x mb-3"></i>
          <div>Kantor Regional XI Badan Kepegawaian Negara<br/>Jl. A.A Maramis Km.8 Kel. Paniki Bawah Kec. Mapanget<br/>Kota Manado 95256, Provinsi Sulawesi Utara
		  </div>
		  
        </div>
        <div class="col-lg-6 mr-auto text-center">
          <i class="fas fa-phone fa-5x mb-3 "></i>
          <!-- Make sure to change the email address in anchor text AND the link below! -->
          <div>Telp. (+62) 431 811090<br/>Fax. (+62) 431 812748</div>
        </div>
      </div>
    </div>
  </section>
  
  <!-- login modal !-->
  <div id="loginModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Please Login!               
            </div>
			
            <div class="modal-body">
                <form class="form" id="formLogin" role="form" autocomplete="off" id="formLogin" novalidate="" method="POST">
                    <div class="form-group">
                        <label for="uname1">Username :</label>
                        <input type="text" class="form-control form-control-sm" name="username" id="username" required>                        
                    </div>
                    <div class="form-group">
                        <label>Password :</label>
                        <input type="password" class="form-control form-control-sm" name="password" id="password" required >                        
                    </div>            
                    
                </form>
            </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="btnLogin">Login</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">Close</button>
			</div>
        </div>
    </div>
</div>

  <!-- Footer -->
  <footer class="bg-light py-5">
    <div class="container">
      <div class="small text-center text-muted">Copyright &copy; 2019 - Kantor Regional XI Badan Kepegawaian Negara</div>
    </div>
  </footer>

  <!-- Bootstrap core JavaScript -->
  <script src="<?php echo base_url()?>assets/vendor/jquery/jquery.min.js"></script>
  <script src="<?php echo base_url()?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

  <!-- Plugin JavaScript -->
  <script src="<?php echo base_url()?>assets/vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="<?php echo base_url()?>assets/vendor/magnific-popup/jquery.magnific-popup.min.js"></script>
  <script src="<?php echo base_url()?>assets/vendor/select2/js/select2.min.js"></script>

  <!-- Custom scripts for this template -->
  <script src="<?php echo base_url()?>assets/js/creative.min.js"></script>
  <script>
	$(document).ready(function() {
		$('.custom-select').select2();
		
		$("#btnLogin").click(function(){
			var username = $("#username").val();
            var password = $("#password").val();
			if( username =='' || password ==''){
				$('input[type="text"],input[type="password"]').css("border","2px solid red");
				$('input[type="text"],input[type="password"]').css("box-shadow","0 0 3px red");
			}else{
				
				$.ajax({
					type: "POST",
					url: "<?php echo site_url()?>/welcome/doLogin",
					data: $("#formLogin").serialize(),
					dataType: "json",
					success: function(data) {						
						if(data.logged_in == false){
							alert(data.msg);
						}else{
							document.location.href = "<?php echo site_url() ?>/welcome";
						}
					},
					error: function() {
						alert('Please call Administrator');
					}
});
			} 
			
		});
		
		$('#loginModal').on('hidden.bs.modal', function () {
			 $("#formLogin").trigger("reset"); 
			 $('input[type="text"],input[type="password"]').css("border","");
			 $('input[type="text"],input[type="password"]').css("box-shadow","");
		});
	
	});
  </script>
</body>

</html>
