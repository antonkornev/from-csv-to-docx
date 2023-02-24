<?php

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class WordCreator
{
    private const WIDTH_COL_FIRST = 3000;
    private const WIDTH_COL_SECOND = 6000;
    private const WIDTH_COL_THIRD = 1000;

    private const HEADER_COL_FIRST = 'Проект (модуль)';
    private const HEADER_COL_SECOND = 'Выполненные работы по Разработке ОИС';
    private const HEADER_COL_THIRD = 'Время';

    private const REPORTS_DIR = 'reports';

    private const DOCX_FORMAT = '.docx';

    private const HOURS_SYMBOL = 'ч';

    private const TOTAL_TIME_LABEL = 'Итого';

    private const ZERO_SPACE_AFTER = ['spaceAfter' => 0];

    private const NEW_LINE = '<w:br/>';

    public static function create($formattedData, $fileName)
    {
        $phpWord = new PhpWord();

        $section = $phpWord->addSection();

        $phpWord->addTableStyle('mainTable', [
            'borderColor' => '006699',
            'borderSize'  => 6,
            'cellMargin'  => 50
        ]);

        $table = $section->addTable('mainTable');

        $table->addRow();
        $cell1 = $table->addCell(self::WIDTH_COL_FIRST);
        $cell1->addText(self::HEADER_COL_FIRST);
        $cell2 = $table->addCell(self::WIDTH_COL_SECOND);
        $cell2->addText(self::HEADER_COL_SECOND);
        $cell3 = $table->addCell(self::WIDTH_COL_THIRD);
        $cell3->addText(self::HEADER_COL_THIRD);

        $timeSum = 0;
        foreach ($formattedData as $taskName => $item) {
            $table->addRow();
            $cell1 = $table->addCell(self::WIDTH_COL_FIRST);
            $cell1->addText($taskName);
            $cell2 = $table->addCell(self::WIDTH_COL_SECOND);
            $cell3 = $table->addCell(self::WIDTH_COL_THIRD);

            foreach ($item as $task) {
                $taskNameDetail = $task['task'];
                $countOfBreaks = intdiv(strlen($taskNameDetail), 70);
                $breaks = $countOfBreaks > 0 ? str_repeat(self::NEW_LINE, $countOfBreaks) : '';
                $cell2->addListItem($taskNameDetail, 0, null, null, self::ZERO_SPACE_AFTER);
                $cell3->addText($task['time'] . self::HOURS_SYMBOL . $breaks, null, self::ZERO_SPACE_AFTER);
                $timeSum += $task['time'];
            }
        }

        $table->addRow();
        $table->addCell(self::WIDTH_COL_FIRST);

        $cell2 = $table->addCell(self::WIDTH_COL_SECOND);
        $cell2->addText(self::TOTAL_TIME_LABEL);

        $cell3 = $table->addCell(self::WIDTH_COL_THIRD);
        $cell3->addText($timeSum . self::HOURS_SYMBOL);

        $objWriter = IOFactory::createWriter($phpWord);
        $file = DIR . DIRECTORY_SEPARATOR . self::REPORTS_DIR . DIRECTORY_SEPARATOR . $fileName . self::DOCX_FORMAT;

        $objWriter->save($file);

        return $file;
    }
}