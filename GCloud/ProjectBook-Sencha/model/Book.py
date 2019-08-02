from google.appengine.ext import ndb


class Book(ndb.Model):
    Id = ndb.StringProperty()
    Name = ndb.StringProperty()
    Type = ndb.StringProperty()
    Date = ndb.DateTimeProperty(auto_now_add=True)