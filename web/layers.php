<?php

function different_type($data) {
    if ($data->in_use ?? 0) {
        $geometry_map = $data->in_use->type;
        $geometry_file = $data->geometry;
        if (preg_match('#Point#', $geometry_file) && $geometry_map != 'POINT') {
            return 1;
        }
        if (preg_match('#Polygon#', $geometry_file) && $geometry_map != 'POLYGON') {
            return 1;
        }
        if (preg_match('#Line String#', $geometry_file) && $geometry_map != 'LINE') {
            return 1;
        }
    }
    return 0;
}

$summary = '/data/vhost/tilma.mysociety.org/layers/summary.json';
if (!file_exists($summary)) {
    print "Only works on live site.";
    exit;
}

$staging = json_decode(file_get_contents('https://tilma.staging.mysociety.org/layers.json'));
$live = json_decode(file_get_contents($summary));

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Tilma asset layers</title>
    <link rel="stylesheet" href="https://gaze.mysociety.org/assets/css/global.css">
    <meta name="viewport" content="initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700,900,400italic' rel='stylesheet' type='text/css'>
    <style>
        h3 { margin-top: 1em; }
        table { width: 100%; }
        td.b { word-break: break-all; }
        tr.e { color: #f00; }
        summary { display: list-item; }
        summary h2 { display: inline-block; padding-top: 0; }
    </style>
  </head>
  <body>
    <div class="ms-header">
      <nav class="ms-header__row">
        <a class="ms-header__logo" href="https://www.mysociety.org">mySociety</a>
      </nav>
    </div>

    <header class="site-header">
      <div class="container">
        <h1>Tilma asset layers</h1>
      </div>
    </header>

    <div class="page-wrapper">
      <div class="page">

        <div class="main-content-column">
          <main role="main" class="main-content">

<?php

print '<h2>Live</h2>';

$cobrand = '';
$child = false;
foreach ($live as $fn => $data) {
    if ($cobrand != $data->directory) {
        if ($cobrand) {
            print "</table>\n";
        }
        if (substr_count($data->directory, '/') && !$child) {
            print "<details> <summary><h2>Child directories (old/unused)</h2></summary>";
            $child = true;
        }
        print "<h3>$data->directory</h3>\n";
        print "<table cellpadding=3><tr><th>File</th><th>Layer</th><th>Date</th><th>Features</th><th>Projection</th><th>Staging</th></tr>\n";
        $cobrand = $data->directory;
    }
    $st = $staging->$fn;
    $date = date('Y-m-d', $data->date);
    $fields = join(', ', $data->fields);
    $display_fn = str_replace($data->directory . '/', '', $fn);
    print "<tr";
    if (different_type($data)) {
        print ' class=e';
    }
    print "><td class=b title='$fields'>$display_fn</td>";
    print "<td>" . (isset($data->in_use) ? $data->in_use->name : '<i>none</i>') . "</td>";
    print "<td>$date</td>";
    print "<td>$data->features</td>";
    print "<td>$data->srid</td>";
    print "<td><small>";
    if ($st) {
        $st_date = date('Y-m-d', $st->date);
        $st_in_use = $st->in_use ?? 0;
        $data_in_use = $data->in_use ?? 0;
        if ($st_date == $date
            && $st->features == $data->features
            && (($st_in_use && $data_in_use && $st_in_use->name == $data_in_use->name) || (!$st_in_use && !$data_in_use))
            && $st->srid == $data->srid) {
            print "<i>Same</i>";
        } else {
            $diff = [];
            if ($st_date != $date) {
                $diff[] = $st_date;
            }
            if ($st->features != $data->features) {
                $diff[] = $st->features;
            }
            if ($st_in_use && $data_in_use && $st_in_use->name != $data_in_use->name) {
                $diff[] = $st_in_use->name;
            } elseif ($st_in_use && !$data_in_use) {
                $diff[] = $st_in_use->name;
            } elseif (!$st_in_use && $data_in_use) {
                $diff[] = '<i>none</i>';
            }
            if ($st->srid != $data->srid) {
                $diff[] = $st->srid;
            }
            print join('<br>', $diff);
        }
    } else {
        print '<i>No</i>';
    }
    print "</small></td></tr>\n";
    unset($staging->$fn);
}
if ($cobrand) {
    print '</table>';
}
if ($child) {
    print '</details>';
}

print '<details> <summary><h2>Only on staging</h2></summary>';

$cobrand = '';
foreach ($staging as $fn => $data) {
    if ($cobrand != $data->directory) {
        if ($cobrand) {
            print "</table>\n";
        }
        print "<h3>$data->directory</h3>\n";
        print "<table cellpadding=3><tr><th>File</th><th>Layer</th><th>Date</th><th>Features</th><th>Projection</th></tr>";
        $cobrand = $data->directory;
    }
    $date = date('Y-m-d', $data->date);
    $fields = join(', ', $data->fields);
    print "<tr";
    if (different_type($data)) {
        print ' class=e';
    }
    print "><td class=b title='$fields'>$fn</td><td>";
    print isset($data->in_use) ? $data->in_use->name : '<i>none</i>';
    print "</td><td>$date</td><td>$data->features</td><td>$data->srid</td></tr>\n";
}
if ($cobrand) {
    print '</table>';
}
print '</details>';
?>

          </main>
        </div>

        <div class="secondary-content-column">
          <nav class="sidebar">
            <ul>
              <li><a href="https://github.com/mysociety/tilma">tilma GitHub</a></li>
              <li><a href="https://github.com/mysociety/fixmystreet.com">fixmystreet.com GitHub</a></li>
              <li><a href="https://github.com/mysociety/fixmystreet">fixmystreet GitHub</a></li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <footer class="site-footer">
    </footer>
  </body>
</html>

