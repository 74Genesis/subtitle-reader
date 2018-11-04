# subtitle-reader
**Сделано для личного использования (Made for personal use)**

Позволяет парсить субтитры из файла или строки и производить различные манипуляции.

### Использование
```
Подключение библиотеки:
use genesis\SubtitleReader\SubtitleReader;

Создаем экземпляр и указываем формат с которым работаем:
$sr = new SubtitleReader('srt');

Загрузка субтитров из файла:
$sr->loadFile("test.srt");

Сохранение субтитров в файл. Поддерживаются форматы: "srt".
$sr->saveAs('srt', "path/to/file.srt");

Загрузка из строки:
$sr->loadString("
    1
    00:15:02,746 --> 00:15:04,996
    Some text...
    ... some text2
");


Время конвертируется в секунды с миллисекундами.

Получение массива субтитров:
$sr->getSubtitlesArray();

Результат:
Array
(
    [0] => Array
        (
            [start] => 902.746
            [end] => 904.996
            [text] => Array
                (
                    [0] => Some text...
                    [1] => ... some text2
                )

        )

)

Получение субтитров в json:
$sr->getSubtitlesJson();
```

###### Форматы
* "srt" - SubRip формат.
* "vtt" - WebVTT формат.
* "ssa" - SubStation Alpha и Advanced версия. Расширения .ssa и .ass.