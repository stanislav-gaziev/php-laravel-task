<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Laravel Task</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="csrf-param" content="_token" />
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </head>
    <body>
        <div class="container mt-4">
            <h1>Laravel Task</h1>
            <div>
                <p>Выберите книгу, чтобы увидеть список ее авторов</p>
                {{ Form::select('books', $booksForSelectForm) }}
                <div class="authors-of-book"></div>
            </div>
            <div>
                <p>Выберите автора, чтобы увидеть суммарную стоимость всех книг этого автора</p>
                {{ Form::select('authors', $authorsForSelectForm) }}
                <div class="sum-of-prices-of-books-of-author"></div>
            </div>
            <div>
                <p>Нажмите на кнопку, чтобы увидеть список всех книг, для которых не указаны авторы</p>
                {{ Form::submit('Получить список', ['class' => 'btn']) }}
                <div class="books-without-authors"></div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $("select[name=books]").change(function() {
                    let id = $(this).val();

                    if (id === '0') {
                        $(".authors-of-book").html('');
                        return false;
                    }

                    $.ajax({
                        url: "{{ route('ajax.book') }}",
                        type: "GET",
                        data: {id: id},
                        success: function(response) {
                            let result;

                            if (response.length === 0) {
                                result = "У книги отсутствуют авторы";
                            } else {
                                result = "<table>";

                                $.each(response, function (index, value) {
                                    result += `<tr><td>${value}</td></tr>`;
                                });

                                result += "</table>";
                            }

                            $(".authors-of-book").html(result);
                        }
                    });
                }); 

                $("select[name=authors]").change(function() {
                    let id = $(this).val();

                    if (id === '0') {
                        $(".sum-of-prices-of-books-of-author").html('');
                        return false;
                    }

                    $.ajax({
                        url: "{{ route('ajax.author') }}",
                        type: "GET",
                        data: {id: id},
                        success: function(response) {
                            let result;

                            if (response === 0) {
                                result = "У автора отсутствуют книги, либо они бесплатные";
                            } else {
                                result = `${response} у. е.`;
                            }

                            $(".sum-of-prices-of-books-of-author").html(result);
                        }
                    });
                }); 

                $(".btn").click(function() {
                    $.ajax({
                        url: "{{ route('ajax.list') }}",
                        type: "GET",
                        success: function(response) {
                            let result;

                            if (response.length === 0) {
                                result = "Книги без авторов (или в целом) отсутствуют";
                            } else {
                                result = "<table>";

                                $.each(response, function (index, value) {
                                    result += `<tr><td>${value}</td></tr>`;
                                });

                                result += "</table>";
                            }

                            $(".books-without-authors").html(result);
                        }
                    });
                }); 
            });
        </script>
    </body>
</html>
