from flask import Blueprint, render_template, redirect, url_for, request
import mysql.connector
import random


fifa = Blueprint('fifa', __name__, url_prefix='/')

@fifa.route('/')
def index():
    return redirect(url_for('fifa.tirage'))



@fifa.route('/tirage', methods=['GET', 'POST'])
def tirage():
    chapeau = []
    nb_team_by_chapeau = 0
    continent = request.args.get('continent')

    teams = get_team_filtre()

    if not continent:
        continent = "tous"
    if not continent == "tous" :
        teams = [dico for dico in teams if dico.get('continent_name') == continent]

    if request.method == 'POST':
        n_team = int(request.form.get('n_team'))
        team_select = request.form.getlist('team_select')
        nb_team_by_chapeau = int(n_team/4)
        for i in range (0, 4):
            chapeau.append(team_select[nb_team_by_chapeau*i:nb_team_by_chapeau*(i+1)]) 

    poules = [chap.copy() for chap in chapeau]
    for chap in poules:
        random.shuffle(chap)
    return render_template('pages/tirage.html', teams=teams, continent=continent, chapeau=chapeau, poules=poules, nb_team_by_chapeau=nb_team_by_chapeau )





def connexion_bdd():
    cnx = mysql.connector.connect(user='root', password='',
                                host='127.0.0.1',
                                database='fifa2k20')
    return cnx

def get_team_filtre():
    teams = []
    cur = connexion_bdd()
    if cur and cur.is_connected():
        with cur.cursor(dictionary=True) as cursor:
            result = cursor.execute(f"SELECT continent.name AS continent_name, team.name AS team_name, team.scoring FROM team JOIN continent ON team.continent_id=continent.id ORDER BY scoring desc")
            rows = cursor.fetchall()
            print(rows)
            for index,row in enumerate(rows):
                row["classement"] = index + 1
                teams.append(row)
            print(teams)
        cur.close()
        
    else:
        print("Could not connect")
    return teams
