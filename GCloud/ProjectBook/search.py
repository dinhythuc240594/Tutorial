import logging
from google.appengine.api import search
import json


def create_book(book):
    book = search.Document(
        doc_id=str(book.Id),
        fields=[
            search.TextField(name='Name', value=book.Name),
            search.TextField(name='Type', value=book.Type),
            search.DateField(name='Date', value=book.Date)
        ]
    )
    logging.info(book)
    return book


def add_book_to_index(book):
    index = search.Index('Books')
    logging.info(index)
    index.put(book)


def search_items(keyword):
    index = search.Index('Books')
    query = search.Query(query_string=keyword)
    results = index.search(query)

    items = []
    for item in results:
        items.append({'id': item.doc_id,
                      'Name': item.field('Name').value,
                      'Type': item.field('Type').value,
                      'Date': item.field('Date').value})

    return json.dumps(items, default=str)


def update_book_to_index(book):
    index = search.Index('Books')
    data = create_book(book)
    add_book_to_index(data)


def delete_index(doc_id):
    index = search.Index('Books')
    index.delete(doc_id)
