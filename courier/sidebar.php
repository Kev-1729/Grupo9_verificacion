  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <div class="dropdown">
   	<a href=".." class="brand-link">
        <?php if($_SESSION['login_type'] == 1): ?>
        <h3 class="text-center p-0 m-0"><b>ADMINISTRADOR</b></h3>
        <?php else: ?>
        <h3 class="text-center p-0 m-0"><b>PERSONAL</b></h3>
        <?php endif; ?>

    </a>
      
    </div>
    <div class="sidebar pb-4 mb-4">
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item dropdown">
            <a href="index.php" class="nav-link nav-home">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>     
          <?php if($_SESSION['login_type'] == 1): ?>
          <li class="nav-item">
            <a href="#" class="nav-link nav-edit_branch">
              <i class="nav-icon fas fa-building"></i>
              <p>
                Sucursal
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="index.php?page=new_branch" class="nav-link nav-new_branch tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Agregar Nuevo</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="index.php?page=branch_list" class="nav-link nav-branch_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Lista</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link nav-edit_staff">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Personal de Sucursal
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="index.php?page=new_staff" class="nav-link nav-new_staff tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Agregar Nuevo</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="index.php?page=staff_list" class="nav-link nav-staff_list tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Lista</p>
                </a>
              </li>
            </ul>
          </li>
        <?php endif; ?>
          <li class="nav-item">
            <a href="#" class="nav-link nav-edit_parcel">
              <i class="nav-icon fas fa-boxes"></i>
              <p>
                Paquetes
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="index.php?page=new_parcel" class="nav-link nav-new_parcel tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Agregar Nuevo</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="index.php?page=parcel_list" class="nav-link nav-parcel_list nav-sall tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p>Lista</p>
                </a>
              </li>
              <?php 
              $status_arr = array("ArtÍculo Aceptado<br/>por mensajería","Recogido","Enviado","En tránsito","Llegó a<br/>su Destino","Sin Entregar","Listo para Recoger","Entregado","Recogido","Intento de<br/>Entrega Fallido");
              foreach($status_arr as $k =>$v):
              ?>
              <li class="nav-item">
                <a href="index.php?page=parcel_list&s=<?php echo $k ?>" class="nav-link nav-parcel_list_<?php echo $k ?> tree-item">
                  <i class="fas fa-angle-right nav-icon"></i>
                  <p><?php echo $v ?></p>
                </a>
              </li>
            <?php endforeach; ?>
            </ul>
          </li>
           <li class="nav-item dropdown">
            <a href="index.php?page=track" class="nav-link nav-track">
              <i class="nav-icon fas fa-search"></i>
              <p>
                Seguimiento Paquete
              </p>
            </a>
          </li>  
           <li class="nav-item dropdown">
            <a href="index.php?page=reports" class="nav-link nav-reports">
              <i class="nav-icon fas fa-file"></i>
              <p>
                Reporte
              </p>
            </a>
          </li>  
        </ul>
      </nav>
    </div>
  </aside>
  <script>
  	$(document).ready(function(){
      var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
  		var s = '<?php echo isset($_GET['s']) ? $_GET['s'] : '' ?>';
      if(s!='')
        page = page+'_'+s;
  		if($('.nav-link.nav-'+page).length > 0){
             $('.nav-link.nav-'+page).addClass('active')
  			if($('.nav-link.nav-'+page).hasClass('tree-item') == true){
            $('.nav-link.nav-'+page).closest('.nav-treeview').siblings('a').addClass('active')
  				$('.nav-link.nav-'+page).closest('.nav-treeview').parent().addClass('menu-open')
  			}
        if($('.nav-link.nav-'+page).hasClass('nav-is-tree') == true){
          $('.nav-link.nav-'+page).parent().addClass('menu-open')
        }

  		}
     
  	})
  </script>