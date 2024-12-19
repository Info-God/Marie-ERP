<!-- <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f5f5f5;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            width: 50%;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input[type="file"] {
            margin-bottom: 10px;
        }

        input[type="submit"] {
            padding: 10px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<?php
// Generate CSRF token and store it in the session
// session_start();
// $csrfToken = bin2hex(random_bytes(32));
// $_SESSION['csrf_token'] = $csrfToken;
?>
<form action="" method="post" enctype="multipart/form-data">
    <h2>CSV File Upload Form</h2>
    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
    <label for="csvFile">Choose a CSV file:</label>
    <br>
    <input type="file" name="csvFile" id="csvFile" accept=".csv">
    <br>
    <input type="submit" value="Upload CSV">
</form>

    <h1>Data Table Example</h1>
    <table>
        <thead>
            <tr>
                <th>Categories</th>
                <th>Gene</th>
                <th>Gene Value</th>
                <th>Overall Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="4">BMI</td>
                <td>FTO</td>
                <td>5</td>
                <td rowspan="4">6</td>
            </tr>
            <tr>
                <td>MC4R</td>
                <td>5</td>
                <td></td>
            </tr>
            <tr>
                <td>BDNF</td>
                <td>5</td>
                <td></td>
            </tr>
            <tr>
                <td>GNPDA2</td>
                <td>5</td>
                <td></td>
            </tr>

        </tbody>
    </table>
</body>

</html> -->

<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
    .checkbox-wrapper-center {
        display: flex;
        /* Use flexbox to align items */
        align-items: center;
        /* Vertically center the items */
        margin-bottom: 10px;
        /* Add some space between the pairs */
    }
</style>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <?php echo form_open_multipart($this->uri->uri_string(), array('id' => 'labour-form')); ?>
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <div class="clearfix"></div>
                        <?php $value = (isset($employee_name) ? $employee_name : ''); ?>
                        <?php $attrs = (isset($employee_name) ? array() : array('autofocus' => true)); ?>
                        <?php echo render_input('employee_name', 'Csv', $value, 'file', $attrs); ?>

                        <?php $value = (isset($article) ? $article->subject : ''); ?>
                        <?php $attrs = (isset($article) ? array() : array('autofocus' => true)); ?>
                        <?php echo render_input('salary', 'Salary', $article->articleNumber, 'text'); ?>

                        <?php $value = (isset($article) ? $article->subject : ''); ?>
                        <?php $attrs = (isset($article) ? array() : array('autofocus' => true)); ?>
                        <?php echo render_input('productivity', 'Productivity', $article->articleNumber, 'hours'); ?>

                        <div class="checkbox checkbox-primary">
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="staff_article_fulltime" name="staff_article" <?php if (isset($article) && $article->staff_article == 1) {
                                                                                                            echo 'checked';
                                                                                                        } ?>>
                                <label for="staff_article_fulltime"><?php echo _l('FullTime'); ?></label>
                            </div>

                            <div class="checkbox-wrapper-center">
                                <input type="checkbox" id="staff_article_foreigner" name="staff_article" <?php if (isset($article) && $article->staff_article == 1) {
                                                                                                                echo 'checked';
                                                                                                            } ?>>
                                <label for="staff_article_foreigner"><?php echo _l('Foreigner'); ?></label>
                            </div>

                            <?php #foreach ($currencies as $currency) {
                                #if (isset($client)) {
                                    #if ($currency['id'] == $client->default_currency) {
                                       # $selected = $currency['id'];
                                    #}
                                #}
                            #}
                            // Do not remove the currency field from the customer profile!
                            #echo render_select('default_currency', $currencies, array('id', 'name', 'symbol'), 'invoice_add_edit_currency', $selected, $s_attrs); ?>

                        </div>

                    </div>
                </div>
            </div>
            <?php if ((has_permission('knowledge_base', '', 'create') && !isset($article)) || has_permission('knowledge_base', '', 'edit') && isset($article)) { ?>
                <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
                    <button type="submit" class="btn btn-info pull-right"><?php echo _l('submit'); ?></button>
                </div>
            <?php } ?>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php init_tail(); ?>
<!-- <script>
    $(function() {
        appValidateForm($('#article-form'), {
            subject: 'required',
            articlegroup: 'required'
        });
    });
</script> -->
</body>

</html>