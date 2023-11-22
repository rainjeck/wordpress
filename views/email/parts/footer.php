<?php
// $year = 2020;

// $current_year = date('Y');

// if ( $year != $current_year ) {
//   $year_text = $year. ' - ' .$current_year;
// } else {
//   $year_text = $year;
// }
?>
                    <table class="container">
                        <tr>
                            <td class="footer">
                                <?php /*/ ?>
                                &copy; <?= $year_text; ?>.
                                <?php /**/ ?>
                                <a href="<?= home_url(); ?>" target="_blank"><?= home_url(); ?></a>
                            </td>
                        </tr>
                    </table>

                    </td>
                </tr>
            </tbody>
        </table>

    </body>

</html>
