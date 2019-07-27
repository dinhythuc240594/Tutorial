from google.appengine.ext import ndb
import webapp2
from Model.Book_model import Book
import jinja2
import os
import json
import logging
from datetime import datetime
import random


class MainPage(webapp2.RequestHandler):
    def get(self):
        if self.request.get('data') == 'json':
            self.response.headers['Content-Type'] = 'text/json'

            next_cursor_str = self.request.get('next_cursor', '')
            previous_cursor_str = self.request.get('previous_cursor', '')

            logging.info(next_cursor_str)
            logging.info(previous_cursor_str)

            res = Book.cursor_pagintaion(previous_cursor_str, next_cursor_str)
            logging.info(res)

            res = json.dumps(res, default=str)
            self.response.out.write(res)

            return

        jinja_environment = self.jinja_environment
        template = jinja_environment.get_template('/index.html')
        self.response.out.write(template.render())

    @property
    def jinja_environment(self):
        jinja_environment = jinja2.Environment(
            loader=jinja2.FileSystemLoader(
                os.path.join(os.path.dirname(__file__), './View')),
            extensions=['jinja2.ext.autoescape'],
            autoescape=True
        )
        return jinja_environment


class FormCreate(webapp2.RequestHandler):
    def post(self):
        book_id = str(random.randint(1, 100))
        id_entity = Book.query(Book.Id == book_id).fetch()
        if book_id != '' and id_entity:
            book_id = str(random.randint(101, 500))
            book_name = self.request.get('book_name')
            book_type = self.request.get('book_type')
            book_date = self.request.get('book_date')
            book_date = datetime.strptime(book_date, "%d/%m/%Y").strftime("%Y-%m-%d")
            book = Book(Id=book_id,
                        Name=book_name,
                        Type=book_type,
                        Date=datetime.strptime(book_date, "%Y-%m-%d"))
            book.put()
        else:
            book_name = self.request.get('book_name')
            book_type = self.request.get('book_type')
            book_date = self.request.get('book_date')
            book_date = datetime.strptime(book_date, "%d/%m/%Y").strftime("%Y-%m-%d")
            book = Book(Id=book_id,
                        Name=book_name,
                        Type=book_type,
                        Date=datetime.strptime(book_date, "%Y-%m-%d"))
            book.put()


class FormUpdate(webapp2.RequestHandler):
    def get(self):
        if self.request.get('update_book_id') != '':
            self.response.out.headers['Content-Type'] = 'text/json'
            book_id = self.request.get('update_book_id')
            json_data = json.dumps([book.to_dict() for book in Book.query(Book.Id==book_id).fetch()], default=str)
            self.response.out.write(json_data)
            return

    def post(self):
        book_id = self.request.get('update_book_id')
        book_name = self.request.get('update_book_name')
        book_type = self.request.get('update_book_type')
        book_date = self.request.get('update_book_date')
        book_date = datetime.strptime(book_date, "%d/%m/%Y").strftime("%Y-%m-%d")
        book = Book.query(Book.Id == book_id).get()
        if book:
            book.Id = book_id
            book.Name = book_name
            book.Type = book_type
            book.Date = datetime.strptime(book_date, '%Y-%m-%d')
            book.put()


class FormDelete(webapp2.RequestHandler):
    def get(self):
        if self.request.get('delete_book_id') != '':
            self.response.out.headers['Content-Type'] = 'text/json'
            book_id = self.request.get('delete_book_id')
            json_data = json.dumps([book.to_dict() for book in Book.query(Book.Id == book_id).fetch()], default=str)
            self.response.out.write(json_data)
            return

    def post(self):
        book_id = self.request.get('delete_book_id')
        book = Book.query(Book.Id == book_id).get()
        if book:
            book.key.delete()


class FormSearch(webapp2.RequestHandler):
    def get(self):
        if self.request.get('data') == 'json':
            self.response.headers['Content-Type'] = 'text/json'
            key_word = self.request.get('key_word')
            data = Book.query(ndb.OR(Book.Name == key_word,
                                     Book.Type == key_word))
            logging.info(data)

            items = json.dumps([book.to_dict() for book in data.fetch()], default=str)
            logging.info(items)

            self.response.out.write(items)
            return


app = webapp2.WSGIApplication([
    ('/', MainPage),
    ('/create/', FormCreate),
    ('/update/', FormUpdate),
    ('/delete/', FormDelete),
    ('/search/', FormSearch),
], debug=True)
