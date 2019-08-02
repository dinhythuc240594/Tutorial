import webapp2
from server.Server import MainPage, FormCreate, FormUpdate, FormDelete

app = webapp2.WSGIApplication([
    ('/', MainPage),
    ('/create/', FormCreate),
    ('/update/', FormUpdate),
    ('/delete/', FormDelete),
], debug=True)
