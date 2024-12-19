<!DOCTYPE html>
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
            <?php $i = 0;
            $count = [];
            unset($rows[0]);
            $categories = array(
                'BMI' => '5',
                'Triglyceride' => '4',
                'LDL cholesterol' => '3',
                'HDL cholesterol' => '3',
                'Glucose (Blood Sugar)' => '7',
                'Skin Aging' => '1',
                'Skin Elasticity' => '1',
                'Pigmentation' => '3',
                'Hair Loss (Baldness)' => '3',
                'Blood Pressure' => '6',
                'Hair thickness' => '1',
                'Caffeine Metabolism' => '2',
                'Vitamin C Metabolism' => '1',
                'Vitamin D Metabolism' => '1',

            );
            foreach ($rows as $key => $row) { ?>
                <?php if ($i == 0) { 
                    $count=$categories[$row[0]]; ?>
                    <tr>
                        <td rowspan="<?php echo $count?>"><?php echo $row[0]; ?></td>
                        <td><?php echo $row[1] ?></td>
                        <td><?php echo $row[2] ?></td>
                        <td rowspan="<?php echo $count?>"><?php echo $row[3] ?></td>
                    </tr>
                <?php } ?>
                <?php if ($i != 0) { ?>
                    <tr>
                        <td><?php echo $row[1] ?></td>
                        <td><?php echo $row[2] ?></td>
                    </tr>
                <?php };
                $i++; 
                if ($i == $count && $i != 0) {
                    $i = 0;
                } ?>
            <?php } ?>
            <!-- <tr>
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
            </tr> -->

        </tbody>
    </table>
</body>

</html>