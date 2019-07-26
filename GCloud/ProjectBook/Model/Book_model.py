from google.appengine.ext import ndb
from google.appengine.datastore.datastore_query import Cursor
import json
import logging


class Book(ndb.Model):
    Id = ndb.StringProperty()
    Name = ndb.StringProperty()
    Type = ndb.StringProperty()
    Date = ndb.DateTimeProperty(auto_now_add=True)

    @classmethod
    def cursor_pagintaion(cls, previous_cursor_str, next_cursor_str):

        logging.info(next_cursor_str)
        logging.info(previous_cursor_str)

        if not previous_cursor_str and not next_cursor_str:
            items, next_cursor, more = cls.query().order(cls.Id).fetch_page(3)
            previous_cursor_str = ''
            if next_cursor:
                next_cursor_str = next_cursor.urlsafe()
            else:
                next_cursor_str = ''

            next_ = True if more else False
            prev = False

        elif next_cursor_str:
            cursor = Cursor(urlsafe=next_cursor_str)
            items, next_cursor, more = cls.query().order(cls.Id).fetch_page(3, start_cursor=cursor)
            previous_cursor_str = next_cursor_str
            next_cursor_str = next_cursor.urlsafe()

            logging.info('next: ' + next_cursor_str + ' - previous: ' + previous_cursor_str)

            prev = True
            next_ = True if more else False

        elif previous_cursor_str:
            cursor = Cursor(urlsafe=previous_cursor_str)
            items, next_cursor, more = cls.query().order(-cls.Id).fetch_page(3, start_cursor=cursor)
            items.reverse()

            logging.info('next: ' + next_cursor_str + ' - previous: ' + previous_cursor_str)

            next_cursor_str = previous_cursor_str
            previous_cursor_str = next_cursor.urlsafe()
            prev = True if more else False
            next_ = True

        items_book = [book.to_dict() for book in items]

        return {'items_book': items_book, 'next_cursor': next_cursor_str, 'previous_cursor': previous_cursor_str, 'prev': prev, 'next': next_}

