<?php
include 'db_connect.php';
$qry = $conn->query("SELECT * FROM parcels where id = ".$_GET['id'])->fetch_array();
foreach($qry as $k => $v){
	$$k = $v;
}
if($to_branch_id > 0 || $from_branch_id > 0){
	$to_branch_id = $to_branch_id  > 0 ? $to_branch_id  : '-1';
	$from_branch_id = $from_branch_id  > 0 ? $from_branch_id  : '-1';
$branch = array();
 $branches = $conn->query("SELECT *,concat(street,', ',city,', ',state,', ',zip_code,', ',country) as address FROM branches where id in ($to_branch_id,$from_branch_id)");
    while($row = $branches->fetch_assoc()):
    	$branch[$row['id']] = $row['address'];
	endwhile;
}
?>
<div class="container-fluid">
	<div class="col-lg-12">
		<div class="row">
			<div class="col-md-12">
				<div class="callout callout-info">
					<dl>
						<dt>Número de Seguimiento:</dt>
						<dd> <h4><b><?php echo $reference_number ?></b></h4></dd>
					</dl>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="callout callout-info">
					<b class="border-bottom border-primary">Información del Remitente</b>
					<dl>
						<dt>Nombre:</dt>
						<dd><?php echo ucwords($sender_name) ?></dd>
						<dt>Dirección:</dt>
						<dd><?php echo ucwords($sender_address) ?></dd>
						<dt>Contacto:</dt>
						<dd><?php echo ucwords($sender_contact) ?></dd>
					</dl>
				</div>
				<div class="callout callout-info">
					<b class="border-bottom border-primary">Información del Recipiente</b>
					<dl>
						<dt>Nombre:</dt>
						<dd><?php echo ucwords($recipient_name) ?></dd>
						<dt>Dirección:</dt>
						<dd><?php echo ucwords($recipient_address) ?></dd>
						<dt>Contacto:</dt>
						<dd><?php echo ucwords($recipient_contact) ?></dd>
					</dl>
				</div>
			</div>
			<div class="col-md-6">
				<div class="callout callout-info">
					<b class="border-bottom border-primary">Detalle del Paquete</b>
						<div class="row">
							<div class="col-sm-6">
								<dl>
									<dt>Peso:</dt>
									<dd><?php echo $weight ?></dd>
									<dt>Alto:</dt>
									<dd><?php echo $height ?></dd>
									<dt>Precio:</dt>
									<dd><?php echo number_format($price,2) ?></dd>
								</dl>	
							</div>
							<div class="col-sm-6">
								<dl>
									<dt>Ancho:</dt>
									<dd><?php echo $width ?></dd>
									<dt>Largo:</dt>
									<dd><?php echo $length ?></dd>
									<dt>Tipo:</dt>
									<dd><?php echo $type == 1 ? "<span class='badge badge-primary'>Delivery</span>":"<span class='badge badge-info'>Recojo</span>" ?></dd>
								</dl>	
							</div>
						</div>
					<dl>
						<dt>La sucursal aceptó el paquete:</dt>
						<dd><?php echo ucwords($branch[$from_branch_id]) ?></dd>
						<?php if($type == 2): ?>
							<dt>Sucursal más cercana al destinatario para recogida</dt>
							<dd><?php echo ucwords($branch[$to_branch_id]) ?></dd>
						<?php endif; ?>
						<dt>Status:</dt>
						<dd>
							<?php 
							switch ($status) {
								case '1':
									echo "<span class='badge badge-pill badge-info'> Recogido</span>";
									break;
								case '2':
									echo "<span class='badge badge-pill badge-info'> Enviado</span>";
									break;
								case '3':
									echo "<span class='badge badge-pill badge-primary'> En tránsito</span>";
									break;
								case '4':
									echo "<span class='badge badge-pill badge-primary'> Llegada a destino</span>";
									break;
								case '5':
									echo "<span class='badge badge-pill badge-primary'> Listo para entrega</span>";
									break;
								case '6':
									echo "<span class='badge badge-pill badge-primary'> Listo para recoger</span>";
									break;
								case '7':
									echo "<span class='badge badge-pill badge-success'>Entregado</span>";
									break;
								case '8':
									echo "<span class='badge badge-pill badge-success'> Recogido</span>";
									break;
								case '9':
									echo "<span class='badge badge-pill badge-danger'> Intento de entrega fallido</span>";
									break;
								
								default:
									echo "<span class='badge badge-pill badge-info'> Artículo aceptado por el mensajero</span>";
									
									break;
							}

							?>
							<span class="btn badge badge-primary bg-gradient-primary" id='update_status'><i class="fa fa-edit"></i> Actualizar Estado</span>
						</dd>

					</dl>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer display p-0 m-0">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
</div>
<style>
	#uni_modal .modal-footer{
		display: none
	}
	#uni_modal .modal-footer.display{
		display: flex
	}
</style>
<noscript>
	<style>
		table.table{
			width:100%;
			border-collapse: collapse;
		}
		table.table tr,table.table th, table.table td{
			border:1px solid;
		}
		.text-cnter{
			text-align: center;
		}
	</style>
	<h3 class="text-center"><b>Resultado</b></h3>
</noscript>
<script>
	$('#update_status').click(function(){
		uni_modal("Update Status of: <?php echo $reference_number ?>","manage_parcel_status.php?id=<?php echo $id ?>&cs=<?php echo $status ?>","")
	})
</script>