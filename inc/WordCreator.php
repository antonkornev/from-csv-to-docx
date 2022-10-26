<?php

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class WordCreator
{
    const FIRST_COL_WIDTH = 3000;
    const SECOND_COL_WIDTH = 6000;
    const THIRD_COL_WIDTH = 1000;

    const FIRST_COL_HEADER = 'Проект (модуль)';
    const SECOND_COL_HEADER = 'Выполненные работы по Разработке ОИС';
    const THIRD_COL_HEADER = 'Время';

    const REPORTS_DIR = 'reports';

    const DOCX_FORMAT = '.docx';

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
        $cell1 = $table->addCell(self::FIRST_COL_WIDTH);
        $cell1->addText(self::FIRST_COL_HEADER);
        $cell2 = $table->addCell(self::SECOND_COL_WIDTH);
        $cell2->addText(self::SECOND_COL_HEADER);
        $cell3 = $table->addCell(self::THIRD_COL_WIDTH);
        $cell3->addText(self::THIRD_COL_HEADER);

        $timeSum = 0;
        foreach ($formattedData as $taskName => $item) {
            $table->addRow();
            $cell1 = $table->addCell(self::FIRST_COL_WIDTH);
            $cell1->addText($taskName);
            $cell2 = $table->addCell(self::SECOND_COL_WIDTH);
            $cell3 = $table->addCell(self::THIRD_COL_WIDTH);

            foreach ($item as $task) {
                $taskNameDetail = $task['task'];
                $countOfBreaks = intdiv(strlen($taskNameDetail), 70);
                $breaks = $countOfBreaks > 0 ? str_repeat('<w:br/>', $countOfBreaks) : '';
                $cell2->addListItem($taskNameDetail, 0, null, null, ['spaceAfter' => 0]);
                $cell3->addText($task['time'] . 'ч' . $breaks, null, ['spaceAfter' => 0]);
                $timeSum += $task['time'];
            }
        }

        $table->addRow();
        $table->addCell(self::FIRST_COL_WIDTH);
        $cell2 = $table->addCell(self::SECOND_COL_WIDTH);
        $cell2->addText('Итого');
        $cell3 = $table->addCell(self::THIRD_COL_WIDTH);
        $cell3->addText($timeSum . 'ч');

        $objWriter = IOFactory::createWriter($phpWord);
        $file = DIR . DIRECTORY_SEPARATOR . self::REPORTS_DIR . DIRECTORY_SEPARATOR . $fileName . self::DOCX_FORMAT;

        $objWriter->save($file);

        return $file;
    }
}