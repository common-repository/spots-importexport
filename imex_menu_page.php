<h2>Spots Import/Export</h2>

<h3>Export</h3>
<p>
    <a href="<?php echo admin_url( 'edit.php?post_type=spot&page=spots-imex&export=csv' ); ?>">Click to export spots</a>
</p>

<h3>Import</h3>
<form enctype="multipart/form-data" method="post" action="">
Spots CSV file: <input type="file" name="spots-csv" />
<br/><?php submit_button( 'Import' ); ?>
</form>