from google.appengine.ext import ndb
import webapp2
import jinja2
import os
import logging
import json
import random
from datetime import datetime
import Search
from model.Book import Book


class MainPage(webapp2.RequestHandler):
    def get(self):
        data = self.request.get('data')
        key_word = self.request.get('key_word')
        
        logging.info(key_word)

        if data == 'json' and key_word == '':
            self.response.headers['Content-Type'] = 'text/json'
            book = Book.query()
            items = json.dumps([item.to_dict() for item in book.fetch()], default=str)
            logging.info(items)
            self.response.out.write(items)
            return

        elif data == 'json' and key_word != '':
            self.response.headers['Content-Type'] = 'text/json'

            key_word = self.request.get('key_word')

            data = Search.search_items(key_word)

            logging.info(data)

            self.response.out.write(json.dumps(data, default=str))
            return            

        jinja_environment = self.jinja_environment
        template = jinja_environment.get_template('/index.html')
        self.response.out.write(template.render())


    @property
    def jinja_environment(self):
        jinja_environment = jinja2.Environment(
            loader=jinja2.FileSystemLoader(
                os.path.join(os.path.dirname(__file__), '../templates')),
            extensions=['jinja2.ext.autoescape'],
            autoescape=True
        )
        return jinja_environment


class FormCreate(webapp2.RequestHandler):
    def post(self):

        logging.info(self.request.get('book_name'))
        logging.info(self.request.get('book_type'))
        logging.info(self.request.get('book_date'))

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

            data = Search.create_book(book)
            Search.add_book_to_index(data)

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

            data = Search.create_book(book)
            Search.add_book_to_index(data)

            book.put()


class FormUpdate(webapp2.RequestHandler):
    def get(self):
        if self.request.get('update_book_id') != '':
            self.response.out.headers['Content-Type'] = 'text/json'
            book_id = self.request.get('update_book_id')

            json_data = json.dumps([book.to_dict() for book in Book.query(Book.Id==book_id).fetch()], default=str)

            logging.info(json_data)

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

            data = Search.update_book_to_index(book)
            logging.info(data)

            book.put()

class FormDelete(webapp2.RequestHandler):
    def get(self):
        if self.request.get('delete_book_id') != '':
            self.response.out.headers['Content-Type'] = 'text/json'
            book_id = self.request.get('delete_book_id')
            json_data = json.dumps([book.to_dict() for book in Book.query(Book.Id == book_id).fetch()], default=str)
            
            logging.info(json_data)

            self.response.out.write(json_data)
            return

    def post(self):
        book_id = self.request.get('delete_book_id')
        book = Book.query(Book.Id == book_id).get()
        if book:
            Search.delete_index(book_id)
            book.key.delete()